// Contains javascript functions for displayingwarnings and errors

function get_dismissable_alert(msg) {
    return `<div class="center-block"> \n\
    <div class="alert alert-warning alert-dismissable" style="display:inline-block;">\n\
        <span type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></span>\n\
        <em>${msg}</em>\n\
    </div>\n\
</div>`;
}

/**
 * Display a dismissable alert message in the #script_alter_block, 
 * optionally, if a job id is present, log the message on the server under the job. 
 * @param {string} msg The text of the alert message to display
 * @param {string} id The optional job id. If present the error will be logged on the server in th ejobs/<id> directory 
 */
function display_dismissable_warning(msg, id=null) {
    $("#script_alert_block").html(get_dismissable_alert(msg));
    const err = new Error();
    if (null === id) {
        return;
    }
    $.post("logClientError", {
        id: `${id}`, 
        msg: `${msg}`,
        stack: `${err.stack}`
    })
}