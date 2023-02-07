//show/hide user sign up form based on consent

window.onload = preparePage();

function preparePage() {
    $("#consentFormCheck").on("change", function()
    {
        if ($("#consentFormCheck")[0].checked)
        {
            //show user sign up form
            $("#getUserDetails").show();

            //change button text
            $("#signUpBtn")[0].value = "Sign up with username and password";
        }
        else
        {
            //hide user sign up form
            $("#getUserDetails").hide();

            //change button text
            $("#signUpBtn")[0].value = "Sign up as a guest";
        }
    })
}