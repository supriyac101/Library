

var optionExtended = {


  productGridCheckboxCheck : function(grid, element, checked) {
        if (element.value > 0) {
				  var f = $('product_ids_string');
          if (element.checked) {
				    if (f.value){
					    var ids = f.value.split(',');
					    if (ids.indexOf(element.value) == -1)
					      ids.push(element.value);
					    grid.reloadParams = {'products[]': ids};						
					    f.value = ids.join(',');
				    } else {
					    grid.reloadParams = {'products[]': element.value};		
					    f.value = element.value;						
				    }	
          } else {            
				    var ids = f.value.split(',');
				    ids = ids.without(element.value);
				    grid.reloadParams = {'products[]': ids};					
				    f.value = ids.join(',');
          }
        }

    },
	
	
    productGridRowClick : function(grid, event) {  
        var tdElement = Event.findElement(event, 'td');
        if($(tdElement).down('a'))
          return;                            
        var trElement = Event.findElement(event, 'tr');       
        if (trElement) {
            var checkbox = Element.select(trElement, 'input');
            if (checkbox[0]) {
                var checked = Event.element(event).tagName == 'INPUT' ? checkbox[0].checked : !checkbox[0].checked;
                grid.setCheckboxChecked(checkbox[0], checked);
            }
        }
    } 
    

	 
};



