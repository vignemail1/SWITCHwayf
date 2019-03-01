<?php

/*------------------------------------------------*/
/*------------------------------------------------*/
// Classes ued to ease API management
/*------------------------------------------------*/
/*------------------------------------------------*/

$topLevelDir = dirname(__DIR__);

require_once($topLevelDir . '/lib/functions.php');

/*
 * This class models an IdP to be easily translated to Json
 */
final class IdpObject
{
    // Attributes required by SELECT2 (cf https://select2.org/data-sources/formats)
    public $id;
    public $text;

    // Attributes required by WAY
    public $entityId;
    public $SSO;
    public $name;
    public $names = array();
    public $protocols;
    public $logo;
    /* group */
    public $type;

    public function __construct($entId, $idp)
    {
        $this->entityId = $entId;
        $this->id = $entId;

        global $language;
        if (!isset($language)) {
            $language = 'fr';
        }
        $this->text = (isset($idp[$language]['Name'])) ? $idp[$language]['Name'] : $idp['Name'];

        foreach ($idp as $key => $value) {
            if ($key == "SSO") {
                $this->SSO = $value;
            }
            if ($key == "Name") {
                $this->name = $value;
            }
            if ($key == "Protocols") {
                $this->protocols = $value;
            }
            if ($key == "Logo") {
                if (sizeof($value) > 0) {
                    $this->logo = $value{"URL"};
                }
            }
            // languages
            if (isset($value{"Name"})) {
                // Assume it's a language
                $this->names{$key} = $value{"Name"};
            }
            // Group
            if ($key == "Type") {
                $this->type = $value;
            }
        }
    }
}

/*
{
      "text": "Group 1",
      "children" : [
        {
            "id": 1,
            "text": "Option 1.1"
        },
        {
            "id": 2,
            "text": "Option 1.2"
        }
      ]
    }
*/
final class IdpGroup
{
    public $text;
    public $children = array();
}

/*
 * The goal of this class is to provide accessors to IDP, in the form on JSON array
 * with pagination
 * Respects Select2 AJAX API => https://select2.org/data-sources/ajax
 */
final class IdpRepository
{
    // The idps in the form of IdpObject
    public $idpObjects = array();

    public function __construct(array $IDProviders = array(), array $previouslySelectedIdps = null)
    {
        if ($previouslySelectedIdps != null) {
            foreach ($previouslySelectedIdps as $selIdp) {
                $idp = new IdpObject($selIdp, $IDProviders[$selIdp]);
                $idp->type = getLocalString('last_used');
                $this->idpObjects[] = $idp;
            }
        }

        foreach ($IDProviders as $key => $value) {

            // Skip categories
            if ($value['Type'] == 'category') {
                continue;
            }

            // Skip incomplete descriptions
            if (!is_array($value) || !isset($value['Name'])) {
                continue;
            }

            $idp = new IdpObject($key, $value);
            $this->idpObjects[] = $idp;
        }
    }

    /*
     * Groups a given array
     */
    private function toGroups($array)
    {
        $result = array();
        $tmp = array();
        foreach ($array as $key => $idpObject) {
            $type = $idpObject->type;

            if (!isset($tmp[$type])) {
                $group = new IdpGroup();
                $group->text = $type;
                $tmp[$type] = $group;
            }
            $tmp[$type]->children[] = $idpObject;
        }

        foreach ($tmp as $key => $idpGroup) {
            $result[] = $idpGroup;
        }
        return $result;
    }

    public function countIdps()
    {
        return sizeof($this->idpObjects);
    }

    /*
     * JSON conversion of all IDPs
     */
    public function toJson()
    {
        return json_encode($this->toGroups($this->idpObjects), JSON_UNESCAPED_SLASHES);
    }

    /*
     * Return a page of the IDPs
     */
    public function toJsonByPage($pageNumber, $pageSize=10)
    {
        return $this->getPage($this->idpObjects, $pageNumber, $pageSize);
    }

    private function getPage($array, $pageNumber, $pageSize)
    {
        $from = ($pageNumber - 1) * $pageSize;

        $idpPage = array_slice($array, $from, $pageSize);

        $result{"results"} = $this->toGroups($idpPage);

        // When using select2 optgroups, the pagination must be named "paginate"
        // $result{"pagination"}{"more"} = (($pageNumber + 1)*$pageSize <= sizeof($array));
        $result{"pagination"}{"more"} = (($pageNumber + 1)*$pageSize <= sizeof($array));

        return json_encode($result, JSON_UNESCAPED_SLASHES);
    }

    /*
     * Return a pge of IDPs matching the $query
     */
    public function toJsonByQuery($query, $pageNumber, $pageSize=10)
    {
        // Search in IdpObject::text, IdpObject::name
        return $this->getPage(
          array_filter(
            $this->idpObjects,
            function ($value) use ($query) {
                return (
                    fnmatch("*".removeAccents($query)."*", removeAccents($value->name), FNM_CASEFOLD)
                 || fnmatch("*".removeAccents($query)."*", removeAccents($value->text), FNM_CASEFOLD)
               );
            }
          ),
          $pageNumber,
          $pageSize
          );
    }
}
