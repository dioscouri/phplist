    /**
     * Resets the filters in a form and submits
     * 
     * @param form
     * @return
     */
    function phplistResetFormFilters(form)
    {
        // loop through form elements
        for(i=0; i<form.elements.length; i++)
        {
            var string = form.elements[i].name;
            if ((string) && (string.substring(0,6) == 'filter'))
            {
                form.elements[i].value = '';
            }
        }
        form.submit();
    }
    
	/**
	 * 
	 * @param {Object} order
	 * @param {Object} dir
	 * @param {Object} task
	 */
	function gridOrdering( order, dir ) 
	{
		var form = document.adminForm;
	     
		form.filter_order.value     = order;
		form.filter_direction.value	= dir;
	
		form.submit();
	}
	
	/**
	 * 
	 * @param id
	 * @param change
	 * @return
	 */
	function gridOrder(id, change) 
	{
		var form = document.adminForm;
		
		form.id.value= id;
		form.order_change.value	= change;
		form.task.value = 'order';
		
		form.submit();
	}
	
	/**
	 * 
	 * @param {Object} divname
	 * @param {Object} spanname
	 * @param {Object} showtext
	 * @param {Object} hidetext
	 */
	function displayDiv (divname, spanname, showtext, hidetext) { 
		var div = document.getElementById(divname);
		var span = document.getElementById(spanname);
	
		if (div.style.display == "none")	{
			div.style.display = "";
			span.innerHTML = hidetext;
		} else {
			div.style.display = "none";
			span.innerHTML = showtext;
		}
	}
	
	/**
	 * 
	 * @param {Object} prefix
	 * @param {Object} newSuffix
	 */
	function switchDisplayDiv( prefix, newSuffix ){
		var newName = prefix + newSuffix;
		var currentSuffixDiv = document.getElementById('currentSuffix');
		var currentSuffix = currentSuffixDiv.innerHTML;	
		var oldName = prefix + currentSuffix;
		var newDiv = document.getElementById(newName);
		var oldDiv = document.getElementById(oldName);
	
		currentSuffixDiv.innerHTML = newSuffix;
		newDiv.style.display = "";
		oldDiv.style.display = "none";
	}

	/**
	 * 
	 * @param {Object} form
	 * @param {Object} task
	 * @param {Object} id
	 */
	function submitForm(form, task, id) 
	{   
		form.task.value = task;
		form.id.value = id;
		form.submit();
	}
	/**
     * Submits form using onsubmit if present
     * @param task
     * @return
     */
    function phplistSubmitForm(task, form)
    {
    	Dsc.submitForm(task, form);
    }
	/**
	 * 
	 * @param {Object} form
	 * @param {Object} task
	 * @param {Object} id
	 */
	function verifySubmitForm( form, task, id, url ) {
		
		// if url is present, do validation
		if (url) {		
			// loop through form elements and prepare an array of objects for passing to server
			var str = new Array();
			for(i=0; i<form.elements.length; i++)
			{
				postvar = {
					name : form.elements[i].name,
					value : form.elements[i].value,
					id : form.elements[i].id
				}
				str[i] = postvar;
			}
			// execute Ajax request to server
            var a=new Request({
            	url : url,
                method:"post",
				data:{"elements":JSON.encode(str)},
				onSuccess: function(response){
                    var resp= JSON.decode(response, false);
                    $("message-container").removeClass("ajax-loading").setHTML(resp.msg);
					if (resp.error != '1') {
						// if no error, submit form
						form.task.value = task;
						if (id) {
							form.id.value = id;
						}
						form.submit();
					}
                }
            }).send();
            
		}	
		else {
			form.task.value = task;
			if (id) {
				form.id.value = id;
			}
			form.submit();
		}
	}	
	
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
    function phplistFormValidation( url, container, task, form ) 
    {
        if (task == 'save' || task == 'apply' || task == 'savenew' || task == 'subscribe_new' || task == 'subscribe_selected' || task == 'unsubscribe_selected' || task == 'subscribeModule') 
        {
            // loop through form elements and prepare an array of objects for passing to server
            var str = new Array();
            for(i=0; i<form.elements.length; i++)
            {
                postvar = {
                    name : form.elements[i].name,
                    value : form.elements[i].value,
                    checked : form.elements[i].checked,
                    id : form.elements[i].id
                };
                str[i] = postvar;
            }
            
            // execute Ajax request to server
            var a=new Request({
            	url : url,
                method:"post",
                data:{"elements": JSON.encode(str)},
                onSuccess: function(response){
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
                }
            }).send();
            
        }
            else 
        {
            form.task.value = task;
            form.submit();
        }
    }	