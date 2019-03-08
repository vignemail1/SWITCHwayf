<?php

use PHPUnit\Framework\TestCase;

require("../lib/idpApiObjects.php");


final class IdpApiTest extends TestCase
{
    public function testCount()
    {
        require("test.idps.php");
        printf("\nNb IDP = %d", count($metadataIDProviders));

        $repo = new IdpRepository($metadataIDProviders);

        $this->assertEquals(
            3,
            $repo->countIdps()
        );
    }
    // École des Ponts ParisTech
    public function testQuery()
    {
        $query="ecole des Ponts";

        require("test.IDProvider.metadata.php");
        $repo = new IdpRepository($metadataIDProviders);
        $this->assertEquals(
          '{"results":[{"text":"Ecole avec accents","children":[{"id":"https://rigal.enpc.fr/idp/shibboleth","text":"\u00c9cole des Ponts ParisTech","entityId":"https://rigal.enpc.fr/idp/shibboleth","SSO":"https://rigal.enpc.fr/idp/profile/Shibboleth/SSO","name":"\u00c9cole des Ponts ParisTech","names":{"en":"\u00c9cole des Ponts ParisTech","fr":"\u00c9cole des Ponts ParisTech"},"protocols":"urn:oasis:names:tc:SAML:1.1:protocol urn:mace:shibboleth:1.0 urn:oasis:names:tc:SAML:2.0:protocol","logo":"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAACQkWg2AAAACXBIWXMAAABIAAAASABGyWs+AAAACXZwQWcAAAAQAAAAEABcxq3DAAAB7ElEQVQoz42Rz2/SYBjHX/rSvmW0sK6WhLLLPIhwdX+C2YWYGdDIMJpF/Z+W6XHxJgtTTCZb0xX5ddOMpWNhbiCQFHugykvXtQIeZjAq030vT57n+X6e7+EB40tUq9WmzgkwTUo+n9l6Xa+f/L2aAti2fXhY43m+WHx/JeBdLufz+Z49fWJZ5/l84T+A1taOjuosy8qywnFctboPgP0vICdLPpYV5vhIJMzz3HA4yr7duRSoVqvn1tnq6mNsDnRdh5CMx++22x1dNyYe13g8njRra+sQQp6f6/f7hvFVEK55PDOtdksQhNRK8s+EQqHQM3qI9tA0EoRAOHzDP+tHCHloz+dmS1XVC5v7ohiGcXCgLt5a7HTasVhsckXX9Wy2G4ncLJUr0Wj0V8LurkTTaGnpNgFhJrM1Aba3cxzHLS/fGY9GiqL8BJrN5snpKULoVXqTdLuPjz9pmgYAUFW12/3iOE46vQkh/PBxH9g2AQDIF4pBURTF4L1EPBAQQvPi3p4CACiXK6IYDIXmE4n4wsJ1lmHe5HaIUqmCv/UfPUwBACRJpih6JZnEeLCx8ZKiUCqVNM2BJMkkCRMP7jcaDdf68xcYY5Zhvg+Hpml6Z7wQuswzC2M86/eTFOU4jmVZjJdxES7D6P32h6voB98GBPZakOIQAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDE1LTA3LTIwVDEwOjUwOjIwKzAyOjAwq4YxQQAAACV0RVh0ZGF0ZTptb2RpZnkAMjAxNS0wNy0yMFQxMDo1MDoyMCswMjowMNrbif0AAAAASUVORK5CYII=","type":"Ecole avec accents"}]}],"pagination":{"more":false}}',
            $repo->toJsonByQuery($query, 1)
        );
    }

    public function getImageTest()
    {
        // Remote logo disabled
        $disableRemoteLogos = true;

        $imageString = "https://super-image.com";

        $this->assertEquals(
        '',
        getImage($imageString)
      );

        $imageString = "data:image/png;base64";

        $this->assertEquals(
        $imageString,
        getImage($imageString)
      );

        // Remote logo enabled
        $disableRemoteLogos = false;

        $imageString = "https://super-image.com";

        $this->assertEquals(
        $imageString,
        getImage($imageString)
      );

        $imageString = "data:image/png;base64";

        $this->assertEquals(
        $imageString,
        getImage($imageString)
      );
    }

    // public function testIdpObject()
    // {
    //     require("test.idps.php");
    //     foreach ($metadataIDProviders as $key => $value) {
    //         if ($key == "http://idp-condorcet.dev.entrouvert.org/idp/saml2/metadata") {
    //             // printf("\nkey = %s\n", $key);
    //             $idp = new IdpObject($key, $value);
    //             // printf("\nidp = %s\n", json_encode($idp));
    //             $this->assertEquals(
    //                   "http://idp-condorcet.dev.entrouvert.org/idp/saml2/metadata",
    //                   $idp->entityId
    //                 );
    //         }
    //     }
    // }

    // public function testToJsonByQuery()
    // {
    //     require("test.IDProvider.metadata.php");
    //
    //     $repo = new IdpRepository($metadataIDProviders);
    //
    //     printf("\n%s\n", $repo->toJsonByQuery("arnoud", 1));
    // }
}
