/* Functions for handling IDP select2 dropdown
 * Author: RENATER
 *
 * Inspired from https://select2.org/data-sources/ajax
 */


/*
 * Format for list items
 */
function formatIdp(idp) {
    return doFormatIdp(idp, idp.logo);
}

function formatIdpNotice(idp) {
    return doFormatIdp(idp, idp.element.attributes["logo"].nodeValue);
}

function doFormatIdp(idp, logo) {
    if (idp.loading) {
        return idp.text;
    }

    if (idp.children == null) {
        // IDP
        var img = "";
        if (logo != null) {
            // Logo present
            img = "<img src='" + logo + "' />";
        } else {
            img = "&nbsp;";
        }

        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__logo'>" + img + "</div>" +
            "<div class='select2-result-repository__title'>" + idp.text + "</div></div>";

        return markup;
    } else {
        if (!idp.hide) {
            // Group
            var markup = "<div class='select2-result-repository clearfix'>" +
                idp.text + "</div>";

            return markup;
        }
    }
}

// Perform input validation on WAYF form for select2
function select2CheckForm() {
    console.log("checkForm ", $('.userIdPSelection option:selected').text());
    if (
        document.IdPList.user_idp && (
            $('.userIdPSelection option:selected').text() == null ||
            $('.userIdPSelection option:selected').text() == ''
        )
    ) {
        console.error("Error validating form");
        return false;
    } else {
        return true;
    }
}