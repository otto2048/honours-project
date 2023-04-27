//show/hide user sign up form based on consent

window.onload = preparePage();

function preparePage() {

    //generate random username
    $("#username")[0].value = generateId(8);

    $("#consentFormCheck").on("change", function()
    {
        if ($("#consentFormCheck")[0].checked)
        {
            //show user password field
            $("#getUserDetails").show();

            //clear password field
            $("#password")[0].value = "";

            //change button text
            $("#signUpBtn")[0].value = "Sign up with username and password";
        }
        else
        {
            //hide user password field
            $("#getUserDetails").hide();

            //fill password field with random password
            $("#password")[0].value = generateId(8);

            //change button text
            $("#signUpBtn")[0].value = "Sign up as a guest";
        }
    })
}

//source: stackoverflow (2015)
//accessed from: https://stackoverflow.com/questions/1349404/generate-random-string-characters-in-javascript
// dec2hex :: Integer -> String
// i.e. 0-255 -> '00'-'ff'
function dec2hex (dec) {
    return dec.toString(16).padStart(2, "0")
}

// generateId :: Integer -> String
function generateId (len) {
    var arr = new Uint8Array((len || 40) / 2)
    window.crypto.getRandomValues(arr)
    return Array.from(arr, dec2hex).join('')
}