/* Functions for handlng IDP select2 dropdown
 * Author: RENATER
 *
 * Inspired from https://select2.org/data-sources/ajax
 */


/*
 * Format for list items
 */
function formatList(idp) {
    if (idp.loading) {
        return idp.text;
    }
    
    if (idp.children == null) {
        // IDP
        var img = "";
        if (idp.logo !== null) {
            // Logo present
            if (idp.logo.toLowerCase().indexOf('data:image') !== 0) {
                // TODO : gérer le cas où on ne veut pas fetcher les logos distants
                // remote logo
                img = "<img src='" + idp.logo + "' />";
            } else {
                // local logo
                img = "<img src='" + idp.logo + "' />";
            }
        } else {
            img = "&nbsp;";
        }
        
        var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__logo'>" + img + "</div>" +
        "<div class='select2-result-repository__title'>" + idp.text + "</div></div>";
        
        return markup;
    } else {
        // Group
        var markup = "<div class='select2-result-repository clearfix'>" +
        idp.text + "</div>";
        
        return markup;
    }
}

/*
 * Format for selected element
 */
function formatSelection(idp) {
    return idp.text;
}
