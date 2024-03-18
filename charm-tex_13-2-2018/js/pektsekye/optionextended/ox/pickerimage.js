
var PickerImage = {};
var pickerImage = {};
PickerImage.Main = Class.create({

	uploaders : {},         	  		
	
	initialize : function(){
		Object.extend(this, PickerImage.Config);
	},


  addRow : function(){ 

    var rowTemplate = new Template(this.row, /(^|.|\r|\n)(\[\[(\w+)\]\])/);

    var nextImageId = this.lastImageId + 1;

    Element.insert($('ox_add_image_row'),{'before': rowTemplate.evaluate({'image_id' : nextImageId})});
    
    this.lastImageId++;		    	
  },
  
  
  loadImage : function(currentUploaderId, src){ 
    var img = $(currentUploaderId+'_img'); 
    img.src = src;
    img.show(); 
    this.handleButtonsSwap(currentUploaderId);    	
  },


  loadUploader : function(selectId){
    var currentUploaderId = 'ox_uploader_'+selectId; 
    this.addUploader(currentUploaderId);
    $(currentUploaderId+'_place_holder').hide();
    $(currentUploaderId+'-browse').show();
  },


  addUploader : function(currentUploaderId){

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

    $(currentUploaderId+'_save').value = response.file;
    $(currentUploaderId+'-new').hide();
    
    this.loadImage(currentUploaderId, response.url);

    $(currentUploaderId+'-old').show();        
  },

  deleteImage : function(selectId){
    var currentUploaderId = 'ox_uploader_'+selectId;
    $(currentUploaderId+'_delete_image').value = 1;    
    $(currentUploaderId+'_save').value = '';
    $(currentUploaderId+'_img').hide();

    if (this.uploaders[currentUploaderId] == undefined){
      this.addUploader(currentUploaderId);
    }  
    this.handleButtonsSwap(currentUploaderId);       
  },

  
	changePopup : function(){
		var popupCheckbox = $('popup');
		var layout = $('layout').value;
		if (layout == 'swap'){
			popupCheckbox.checked = false;
			popupCheckbox.disabled = true;
		} else {
			popupCheckbox.disabled = false;			
		}	
	}
		
}); 



