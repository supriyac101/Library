function theRotator() {
    //Set the opacity of all images to 0
    jQuery('div.nowShowing ul li').css({ opacity: 0.0 });

    //Get the first image and display it (gets set to full opacity)
    jQuery('div.nowShowing ul li:first').css({ opacity: 1.0 });

    //Call the rotator function to run the slideshow, 6000 = change to next image after 6 seconds
    setInterval('rotate()', 5000);

}

function theRotator2() {
    //Set the opacity of all images to 0
    jQuery('div.comingSoon ul li').css({ opacity: 0.0 });

    //Get the first image and display it (gets set to full opacity)
    jQuery('div.comingSoon ul li:first').css({ opacity: 1.0 });

    //Call the rotator function to run the slideshow, 6000 = change to next image after 6 seconds
    setInterval('rotate2()', 5000);

}

function rotate() {
    //Get the first image
    var current = (jQuery('div.nowShowing ul li.show') ? jQuery('div.nowShowing ul li.show') : jQuery('div.nowShowing ul li:first'));

    if (current.length == 0) current = jQuery('div.rotator ul li:first');

    //Get next image, when it reaches the end, rotate it back to the first image
    var next = ((current.next().length) ? ((current.next().hasClass('show')) ? jQuery('div.nowShowing ul li:first') : current.next()) : jQuery('div.nowShowing ul li:first'));

    //Un-comment the 3 lines below to get the images in random order

    //var sibs = current.siblings();
    //var rndNum = Math.floor(Math.random() * sibs.length );
    //var next = jQuery( sibs[ rndNum ] );

    //Set the fade in effect for the next image, the show class has higher z-index
    next.css({ opacity: 0.0 })
	    .addClass('show')
	    .animate({ opacity: 1.0 }, 1000);

    //Hide the current image
    current.animate({ opacity: 0.0 }, 1000)
	    .removeClass('show');
};

function rotate2() {
    //Get the first image
    var current = (jQuery('div.comingSoon ul li.show2') ? jQuery('div.comingSoon ul li.show2') : jQuery('div.comingSoon ul li:first'));

    if (current.length == 0) current = jQuery('div.comingSoon ul li:first2');

    //Get next image, when it reaches the end, rotate it back to the first image
    var next = ((current.next().length) ? ((current.next().hasClass('show2')) ? jQuery('div.comingSoon ul li:first') : current.next()) : jQuery('div.comingSoon ul li:first'));

    //Un-comment the 3 lines below to get the images in random order

    //var sibs = current.siblings();
    //var rndNum = Math.floor(Math.random() * sibs.length );
    //var next = jQuery( sibs[ rndNum ] );

    //Set the fade in effect for the next image, the show class has higher z-index
    next.css({ opacity: 0.0 })
	    .addClass('show2')
	    .animate({ opacity: 1.0 }, 1000);

    //Hide the current image
    current.animate({ opacity: 0.0 }, 1000)
	    .removeClass('show2');
};

//CONTROLLING EVENTS IN jQuery
jQuery(document).ready(function () {
    //Load the slideshow
    theRotator();
    theRotator2();
    jQuery('div.nowShowing').fadeIn(1000);
    jQuery('div.nowShowing ul li').fadeIn(1000); // tweak for IE
    jQuery('div.comingSoon').fadeIn(1000);
    jQuery('div.comingSoon ul li').fadeIn(1000); // tweak for IE
	
	jQuery("#owlDemo6").owlCarousel({

            //autoPlay: 2000, //Set AutoPlay to 3 seconds
		pagination:false,
		navigation : true,
		items : 5,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [979,3],
		afterAction: function(el){
       this
       .$owlItems
       .removeClass('active')
	   this
       .$owlItems
       .removeClass('activeNew');
	   var item_count =5; 
	   if(window.innerWidth <= 480){
		   item_count =2;
		}
       this
       .$owlItems
       .eq(this.currentItem + item_count)
       .addClass('active')
	   
	   this
       .$owlItems
       .eq(this.currentItem )
       .addClass('activeNew')
      
     },

  });
  jQuery("#owlDemo1").owlCarousel({

      //autoPlay: 2000, //Set AutoPlay to 3 seconds
		pagination:false,
		navigation : true,
      items : 4,
      itemsDesktop : [1199,4],
      itemsDesktopSmall : [979,4],
	  afterAction: function(el){
       this
       .$owlItems
       .removeClass('active')
	   this
       .$owlItems
       .removeClass('activeNew');
	   var item_count =4; 
	   if(window.innerWidth <= 480){
		   item_count =2;
		}
       this
       .$owlItems
       .eq(this.currentItem + item_count)
       .addClass('active')
	   
	   this
       .$owlItems
       .eq(this.currentItem )
       .addClass('activeNew')
      
     },

  });
jQuery("#fullSlider").owlCarousel({
		autoPlay: 3000,
      navigation : true, // Show next and prev buttons
      slideSpeed : 300,
      paginationSpeed : 400,
	  pagination:false,
      singleItem:true


  });
   resizeContent();
jQuery(window).resize(function() {
	resizeContent();
});
function resizeContent() {
var menuHt =jQuery('.allCateDrop').height();
jQuery('.main-container').css('min-height',menuHt);

if(jQuery(window).width() <999){
	jQuery('.navigationPan').addClass('mobileCate');
	/*jQuery('.mobileCate .allCateClick').click(function(){
		jQuery(this).next().slideToggle();
		
	});*/
}
else{
	jQuery('.navigationPan').removeClass('mobileCate');
}

}
jQuery('#tabs li').click(function(){
			var i = jQuery(this).index();
			jQuery('#tabs li').removeClass('active');
			jQuery(this).addClass('active');
			jQuery('#tabContent .tabDetails').hide().removeClass('active');
			jQuery('#tabContent .tabDetails:eq('+i+')').show().addClass('active');
			jQuery("#owlDemo3,#owlDemo4,#owlDemo5").owlCarousel({

      //autoPlay: 2000, //Set AutoPlay to 3 seconds
		pagination:false,
		navigation : true,
      items : 4,
      itemsDesktop : [1199,3],
      itemsDesktopSmall : [979,3],
	  afterAction: function(el){
       this
       .$owlItems
       .removeClass('active')
	   this
       .$owlItems
       .removeClass('activeNew');
	   var item_count =4; 
	   if(window.innerWidth <= 480){
		   item_count =2;
		}
       this
       .$owlItems
       .eq(this.currentItem + item_count)
       .addClass('active')
	   
	   this
       .$owlItems
       .eq(this.currentItem )
       .addClass('activeNew')
      
     },

  });
    jQuery("#owlDemo2").owlCarousel({

		pagination:false,
		navigation : true,
      items : 4,
      itemsDesktop : [1199,3],
      itemsDesktopSmall : [979,3],
	  afterAction: function(el){
       this
       .$owlItems
       .removeClass('active')
	   this
       .$owlItems
       .removeClass('activeNew');
	   var item_count =4; 
	   if(window.innerWidth <= 480){
		   item_count =2;
		}
       this
       .$owlItems
       .eq(this.currentItem + item_count)
       .addClass('active')
	   
	   this
       .$owlItems
       .eq(this.currentItem )
       .addClass('activeNew')
      
     },

  });
	});
	jQuery(".allCateDrop:not('.cms-home .allCateDrop')").hide();
	jQuery(".allCateClick:not('.cms-home .allCateClick')").click(function() {
        jQuery(this).next('.allCateDrop').slideToggle();
    });
	
	

equalheight = function(container){
var currentTallest = 0,
     currentRowStart = 0,
     rowDivs = new Array(),
     $el,
     topPosition = 0;
 jQuery(container).each(function() {
   $el = jQuery(this);
   jQuery($el).height('auto')
   topPostion = $el.position().top;
   if (currentRowStart != topPostion) {
     for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
       rowDivs[currentDiv].height(currentTallest);
     }
     rowDivs.length = 0; // empty the array
     currentRowStart = topPostion;
     currentTallest = $el.height();
     rowDivs.push($el);
   } else {
     rowDivs.push($el);
     currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
  }
   for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
     rowDivs[currentDiv].height(currentTallest);
   }
 });
}
jQuery(window).load(function() {
  equalheight('.proListBox ul li');
});
jQuery(window).resize(function(){
  equalheight('.proListBox ul li');
});
jQuery(document).ajaxSuccess(function(){
  equalheight('.proListBox ul li');
});
jQuery('li:has(ul)').addClass('parent');
});




