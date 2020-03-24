<?php // Copyright (c) 2019 Geoffroy Arnoud, Guillaume Rousse, and SWITCHwayf contributors

/*------------------------------------------------*/
/*------------------------------------------------*/
// Classes used to ease API management
/*------------------------------------------------*/
/*------------------------------------------------*/

$topLevelDir = dirname(__DIR__);

require_once($topLevelDir . '/lib/functions.php');

function getImage($imageString)
{
    global $disableRemoteLogos;

    $image = '';

    $pos = strpos($imageString, "data:image");

    if ($pos === false) {
        // remote image
        if (!$disableRemoteLogos) {
            $image = $imageString;
        }
    } else {
        // embedded image
        $image = $imageString;
    }

    return $image;
}

/*
 * This class models an IdP to be easily translated to Json
 */
final class IdpObject
{
    // Attributes required by SELECT2 (cf https://select2.org/data-sources/formats)
    public $id;
    public $text;

    public $name;
    public $logo;
    /* group */
    public $type;


    public function __construct($entId, $idp)
    {
        // $this->entityId = $entId;
        $this->id = $entId;

        global $language;
        if (!isset($language)) {
            $language = 'fr';
        }
        $this->text = (isset($idp[$language]['Name'])) ? $idp[$language]['Name'] : $idp['Name'];

        foreach ($idp as $key => $value) {
            if ($key == "Name") {
                $this->name = $value;
            }
            if ($key == "Logo") {
                if (sizeof($value) > 0) {
                    $this->logo = getImage($value{"URL"});
                }
            }
            // Group
            if ($key == "Type") {
                $this->type = $value;
            }
        }
    }

    /*
     * Legacy data select field construction
     * to enable serach as usual
     */
    public function getDataForSearch()
    {
        global $IDProviders;
        return buildIdpData($IDProviders[$this->id], $this->id);
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
    public $hide;
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
        global $showNumOfPreviouslyUsedIdPs;


        if (isset($previouslySelectedIdps) && count($previouslySelectedIdps) > 0) {
            $counter = (isset($showNumOfPreviouslyUsedIdPs)) ? $showNumOfPreviouslyUsedIdPs : 3;
            for ($n = count($previouslySelectedIdps) - 1; $n >= 0; $n--) {
                if ($counter <= 0) {
                    break;
                }

                $selIdp = $previouslySelectedIdps[$n];

                $selIdp = $previouslySelectedIdps[$n];
                if (isset($IDProviders[$selIdp])) {
                    $idp = new IdpObject($selIdp, $IDProviders[$selIdp]);
                    $idp->type = getLocalString('last_used');
                    $this->idpObjects[] = $idp;
                    $counter--;
                }
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
    private function toGroups($array, $hideFirstGroup = false)
    {
        $result = array();
        $tmp = array();

        $firstGroup = true;
        $firstGroupName = '';
        if (!empty($array)) {
            $firstGroupName = $array[0]->type;
            // logInfo(sprintf("firstGroupName = %s", $firstGroupName));
        }

        foreach ($array as $key => $idpObject) {
            $type = $idpObject->type;

            if (!isset($tmp[$type])) {
                $group = new IdpGroup();
                $group->text = $type;
                $tmp[$type] = $group;
                $group->hide = $hideFirstGroup && ($type == $firstGroupName);
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

        $hideFirstGroup = false;
        if (isset($pageNumber) && $pageNumber > 1) {

            // Get last from previous page
            $lastPageLastGroup = $this->idpObjects[$pageNumber * $pageSize - 1]->type;
            $thisPageFirstGroup = $this->idpObjects[$pageNumber * $pageSize]->type;
            $hideFirstGroup = ($lastPageLastGroup == $thisPageFirstGroup);
            // logInfo(sprintf("lastPageLastGroup = %s / thisPageFirstGroup = %s", $lastPageLastGroup, $thisPageFirstGroup));
        }
        // logInfo(sprintf("hideFirstGroup = %s", $hideFirstGroup?"true":"false"));
        $result{"results"} = $this->toGroups($idpPage, $hideFirstGroup);

        $result{"pagination"}{"more"} = (($pageNumber + 1)*$pageSize <= sizeof($array));

        return json_encode($result, JSON_UNESCAPED_SLASHES);
    }

    /*
     * Return a page of IDPs matching the $query.
     * Search is done in:
     * - name/text
     * - getDomainNameFromURI(entityId)
     * - composeOptionData(IDProviders[entityId])
     */
    public function toJsonByQuery($query, $pageNumber, $pageSize=10)
    {
        // Search in IdpObject::text, IdpObject::name
        return $this->getPage(
          array_filter(
            $this->idpObjects,
            function ($value) use ($query) {
                // logDebug(sprintf("Data(%s) = %s", $value->id, $value->getDataForSearch()));
                return (
                    fnmatch("*".removeAccents($query)."*", removeAccents($value->name), FNM_CASEFOLD)
                 || fnmatch("*".removeAccents($query)."*", removeAccents($value->text), FNM_CASEFOLD)
                 || fnmatch("*".removeAccents($query)."*", removeAccents($value->getDataForSearch()), FNM_CASEFOLD)
               );
            }
          ),
          $pageNumber,
          $pageSize
          );
    }
}
