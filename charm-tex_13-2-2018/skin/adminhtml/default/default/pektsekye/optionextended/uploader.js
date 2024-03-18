

OptionExtended.Main.addMethods({
 
  loadImage : function(currentUploaderId, src){ 
    var img = $(currentUploaderId+'_img'); 
    img.src = src;
    img.show(); 
    this.handleButtonsSwap(currentUploaderId);    	
  },


  loadUploader : function(selectId){
    var currentUploaderId = this.getCurrentUploaderId(selectId);
    var value = $(currentUploaderId+'_save').value; 
    if (value.isJSON() && value.evalJSON().url) 
      this.loadImage(currentUploaderId, value.evalJSON().url);
    else 
      this.addUploader(currentUploaderId);
    $(currentUploaderId+'_place_holder').hide();
    $(currentUploaderId+'_row').show();
  },


  addUploader : function(currentUploaderId){
   
   var uploaderTemplate = new Template(this.uploaderTemplate, /(^|.|\r|\n)(\[\[(\w+)\]\])/);

   Element.insert($(currentUploaderId+'_save').up('td'), {'top' :uploaderTemplate.evaluate({'idName' : currentUploaderId})});

   var uploaderConfig = Object.toJSON(this.uploaderConfig).evalJSON();

   uploaderConfig.elementIds.container = this.uploaderConfig.elementIds.container.replace(this.uploaderId, currentUploaderId);
   uploaderConfig.elementIds.templateFile = this.uploaderConfig.elementIds.templateFile.replace(this.uploaderId, currentUploaderId); 
   uploaderConfig.elementIds.browse[0] = this.uploaderConfig.elementIds.browse[0].replace(this.uploaderId, currentUploaderId);
   uploaderConfig.elementIds.delete = this.uploaderConfig.elementIds.delete.replace(this.uploaderId, currentUploaderId); 
             
   var uploader = new Uploader(uploaderConfig);


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
      alert(this.expiredMessage);
      response = '{"error":" "}'; 
    }
    uploader.onFileSuccess(file, response);
  }.bind(this));      

  this.uploaders[currentUploaderId] = 1;
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
      alert(this.expiredMessage); 
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

  deleteImage : function(selectId){
    var currentUploaderId = this.getCurrentUploaderId(selectId);
    
    $(currentUploaderId+'_save').value = '{}';
    $(currentUploaderId+'_img').hide();

    if (this.uploaders[currentUploaderId] == undefined)
      this.addUploader(currentUploaderId);
      
    this.handleButtonsSwap(currentUploaderId);       
  },
  
  getCurrentUploaderId : function(selectId){
    return 'ox_uploader_'+selectId;
  }
     
	
}); 



