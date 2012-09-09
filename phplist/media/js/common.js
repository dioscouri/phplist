
	 /**
     * Overriding core submitbutton task to perform our onsubmit function
     * without submitting form afterwards
     * 
     * @param task
     * @return
     */
    function submitbutton(task) 
    {
        if (task) 
        {
            document.adminForm.task.value = task;
        }

        if (typeof document.adminForm.onsubmit == "function") 
        {
            document.adminForm.onsubmit();
        }
            else
        {
            submitform(task);
        }
    }

	   /**
     * Sends form values to server for validation and outputs message returned.
     * Submits form if error flag is not set in response
     * 
     * @param {String} url for performing validation
     * @param {String} form element name
     * @param {String} task being performed
     */
    function phplistFormValidation(  url, container, task, form, doModal, msg, onCompleteFunction ) 
    {
        if (doModal != false) { Dsc.newModal(msg); }
    	
        if (task == 'save' || task == 'apply' || task == 'savenew' || task == 'subscribe_new' || task == 'subscribe_selected' || task == 'unsubscribe_selected' || task == 'subscribeModule') 
        {
            // loop through form elements and prepare an array of objects for passing to server
            var str = new Array();
            for(i=0; i<form.elements.length; i++)
            {
                if (form.elements[i].name) {
                    postvar = {
                            name : form.elements[i].name,
                            value : form.elements[i].value,
                            checked : form.elements[i].checked,
                            id : form.elements[i].id
                        };
                    str[i] = postvar;
                }
            }
            
            // execute request to server
            var a = new Request({
                url: url,
                method:"post",
                data:{"elements":JSON.encode(str)},
                onSuccess: function(response){
                    var resp = JSON.decode(response, false);
                    if (resp.error != '1')
                    {
                        if (typeof onCompleteFunction == 'function') {
                            onCompleteFunction();
                        }
                        form.task.value = task;
                        form.submit();
                    } else {
                        if (document.id(container)) { document.id(container).set( 'html', resp.msg); }
                    }
                    if (doModal != false) { (function() { document.body.removeChild( document.getElementById('dscModal') ); }).delay(500); }
                }
            }).send();
            
        }
            else 
        {
            form.task.value = task;
            form.submit();
        }
    }