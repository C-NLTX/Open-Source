(function(window) {


    function init()
    {
        let footer = window.document.createElement('div');
        footer.classList = "jbpm-generic-callout-footer";
        footer.innerHTML = `<p>This is my awesome footer injected via javascript and styled via css
                <input type='button' class='btn btn-primary' value='close' id="jbpm-generic-callout-close-footer" />
        </p>`;
        window.document.body.appendChild(footer);

        // we could just reference closeFooter directly but we want to show that we can access the window object here.
        footer.querySelector("#jbpm-generic-callout-close-footer").addEventListener('click', window.jbpmgenericcalloutAPI.closeFooter);
    }

    function closeFooter()
    {
        let footer = window.document.querySelector('.jbpm-generic-callout-footer');
        footer.parentNode.removeChild(footer);
    }

    // two options to hook into
    // window.addEventListener('DOMLoaded', init);
    window.addEventListener('load', init);

    // we wrap everything in an IIFE (Immediately Invoked Function Expression so we can make sure we don't leak anything
    // into the global scope of the system
    let jbpmgenericcalloutAPI = {
        closeFooter : closeFooter
    };

    // expose any api object we need to the main window.  This is useful for working with other modules that depend
    // on this javascript or for accessing api functions that are in the same origin but nested inside a frame
    // IE you can use window.top.skeletonAPI.closeFooter() inside a nested iframe if you need to do something
    // there.
    window.jbpmgenericcalloutAPI = jbpmgenericcalloutAPI;

})(window || {});