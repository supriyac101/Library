

var optionExtended = {



  showSelect : function(type){
        
    if (this.hasOptions){
    
      var select,i,l,ll,selected;
      var childrenField = $('children');

      if (this.rowIdIsSelected == undefined)
        this.checkChildren(childrenField);


      if (type == 'detailed'){
        select = $('detailed_select');
        select.options[0].selected = false;
        l = select.options.length;
        for (i=1;i<l;i++){
          if (this.rowIdIsSelected[select.options[i].value] != undefined) 
            select.options[i].selected = true;
          else
            select.options[i].selected = false;
        }        
      } else {
        select = $('short_select');
        select.options[0].selected = false;          
        l = select.options.length;
        for (i=1;i<l;i++){
          selected = true;
          ll = this.rowIdsByOption[select.options[i].value].length;	
          while (ll--){		
            if (this.rowIdIsSelected[this.rowIdsByOption[select.options[i].value][ll]] == undefined){
              selected = false;
              break;
            }  
          }       						
          select.options[i].selected = selected;
        }         
      }
      
      childrenField.hide();
      select.show();
      select.focus();
      $('show_link').hide();

    }

  },



  showInput : function(type){
    var select;      	
    var input = $('children');		  	
                
    if (type == 'detailed'){		
      select = $('detailed_select');
      var ids = $F(select);
      if (ids[0] == '')
        ids.shift();
      this.resetSelected(ids);			  		  			  
      input.value = ids.join(',');								  					
    } else {		
      select = $('short_select');
      if (this.childrenShortSelectWasChanged != undefined){        	
        var a = $F(select);
        var ids = [];		      	  
        var l = a.length;
        for (var i=0;i<l;i++)
          if (a[i] != '')
            ids = ids.concat(this.rowIdsByOption[a[i]]);
        this.resetSelected(ids);           			      	           
        input.value = ids.join(',');
        delete this.childrenShortSelectWasChanged;
      }	    				
    }  	
          
    select.hide();      		
    input.show();					       					
    $('show_link').show();	
  },



  checkChildren : function(input){
  
    var value = input.value;     
    var ids = [];
    
    if (value != '') {						
      var s = '['+value+']';
      try {
        var ch = s.evalJSON();
        var t = [];
        var l = ch.length;
        for (var i=0;i<l;i++){
          if (this.rowIdIsset[ch[i]] != undefined && t[ch[i]] == undefined){
            ids.push(ch[i]);
            t[ch[i]] = 1;           
          }
        }  
        input.value = ids.join(',');                        		
      } catch (e){
        input.value = '';      
      }            
    }
    
    this.resetSelected(ids);
  },


  resetSelected : function(ids){
    this.rowIdIsSelected = [];	  
    var l = ids.length;
    while (l--)
      this.rowIdIsSelected[ids[l]] = 1;
  },

  
  onChildrenShortSelectChange : function(){
    this.childrenShortSelectWasChanged = 1; 	
  },		


  loadImage : function(currentUploaderId, src){ 
    var img = $(currentUploaderId+'_img'); 
    img.src = src;
    img.show(); 
    this.handleButtonsSwap(currentUploaderId);    	
  },


  loadUploader : function(){
    var currentUploaderId = 'ox_uploader'; 
    var value = $(currentUploaderId+'_save').value; 
    if (value.isJSON()){ 
      this.loadImage(currentUploaderId, value.evalJSON().url);
    }
    this.addUploader(currentUploaderId);
  },


  addUploader : function(currentUploaderId){

   var uploader = new Uploader(this.uploaderConfig);

    document.on('uploader:fileSuccess', function(event) {
        var memo = event.memo;
        if(this._checkCurrentContainer(currentUploaderId, memo.containerId)) {
            this.handleUploadComplete(currentUploaderId, [{response: memo.response}]);
            this.handleButtonsSwap(currentUploaderId);
        }
    }.bind(this));
    document.on('uploader:fileError', function(event) {
        var memo = event.memo;
        if(this._checkCurrentContainer(currentUploaderId, memo.containerId)) {
            this.handleButtonsSwap(currentUploaderId);
        }
    }.bind(this));
    document.on('upload:simulateDelete', this.handleFileRemoveAll.bind(this, currentUploaderId));
    document.on('uploader:simulateNewUpload', this.handleFileNew.bind(this, currentUploaderId));

    uploader.uploader.on('filesSubmitted', function(){uploader.upload()});
  
    uploader.uploader.off('fileSuccess');
    uploader.uploader.on('fileSuccess', function(file, response){
      if (!response.isJSON()) {
        alert('Your session has been expired. Please, reload the page.');
        response = '{"error":" "}'; 
      }
      uploader.onFileSuccess(file, response);
    }.bind(this));      

  },

  _checkCurrentContainer: function (currentUploaderId, child) {
    return $(currentUploaderId).down('#' + child);
  },

  handleFileRemoveAll: function(currentUploaderId, e) {
    if (e.memo && this._checkCurrentContainer(currentUploaderId, e.memo.containerId)) {
      $(currentUploaderId+'-new').hide();
      $(currentUploaderId+'-old').show();
    }
  },
  
  handleFileNew: function (currentUploaderId, e) {
    if (e.memo && this._checkCurrentContainer(currentUploaderId, e.memo.containerId)) {
      $(currentUploaderId + '-new').show();
      $(currentUploaderId + '-old').hide();
      this.handleButtonsSwap(currentUploaderId);
    }
  },
   
  handleButtonsSwap: function (currentUploaderId) {
    $$(['#' + currentUploaderId+'-browse', '#'+currentUploaderId+'-delete']).invoke('toggle');
  },    

  handleUploadComplete: function (currentUploaderId, files) {
    var item = files[0];
    if (!item.response.isJSON()) {
      alert('Your session has been expired. Please, reload the page.'); 
      return;
    }

    var response = item.response.evalJSON();
    if (response.error) {
      return;
    }

    $(currentUploaderId+'_save').value = Object.toJSON(response);
    $(currentUploaderId+'-new').hide();

    this.loadImage(currentUploaderId, response.url);

    $(currentUploaderId+'-old').show();        
  },

  deleteImage : function(){
    var currentUploaderId = 'ox_uploader';
    
    $(currentUploaderId+'_save').value = '{}';
    $(currentUploaderId+'_img').hide();
      
    this.handleButtonsSwap(currentUploaderId);       
  }
  
}; 



