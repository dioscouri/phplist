	/**
     * Submits module form using onsubmit if present
     * @param task
     * @return
     */
    function phplistSubmitModuleForm(task)
    {
    	document.modPhplistSubscribeForm.task = task;
        document.modPhplistSubscribeForm.task.value = task;

        if (typeof document.modPhplistSubscribeForm.onsubmit == "function") 
        {
            document.modPhplistSubscribeForm.onsubmit();
        }
            else
        {
            document.modPhplistSubscribeForm.submit();
        }
    }
    
    function NewsletterisChecked(isitchecked){
    	
    	if (isitchecked == true){
    		document.modPhplistSubscribeForm.boxchecked.value++;
    	}
    	else {
    		document.modPhplistSubscribeForm.boxchecked.value--;
    	}
    }
    
    
