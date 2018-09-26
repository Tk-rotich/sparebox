
(function ($) {
  "use strict";

  $.widget("pektsekye.ymmRestriction", { 

     
    _create : function () {

      $.extend(this, this.options);
      
      this.restrictionArea = $('#ymm_restriction');
      this.searchField = $('#ymm_search_field');    		
      this.notFoundMessage = $('#ymm_not_found')
      this.resultSelect = $('.ymm-result-select');    
      this.addButton = $('.ymm-add-button');
      
      this.replaceToolTip(); //display wider toolTip message

      this._on({ 
        "keypress #ymm_search_field": $.proxy(this.preventSubmit, this), //prevent submitting product when user clicks Enter in the search field                     
        "click button.ymm-search-button": $.proxy(this.searchValues, this),   
        "click button.ymm-add-button": $.proxy(this.addValue, this)                                                        
      });         
              
    },
  
  
    replaceToolTip : function(){
      this.element.find('.woocommerce-help-tip').replaceWith('<span class="woocommerce-help-tip"></span>');
      this.element.find('.woocommerce-help-tip').tipTip({
        fadeIn:50,
        fadeOut:50,
        delay:200,
        enter:function(){
          $(this.tiptip_holder).css("maxWidth","400px")
          $(this.tiptip_content).css("maxWidth","400px");        
        },
        exit:function(){
          $(this.tiptip_holder).fadeOut(0).css("maxWidth", this.maxWidth);      
          $(this.tiptip_content).css("maxWidth", '');          
        },
        content:this.toolTipMessage      
      })
    },

  
    preventSubmit : function(event){
      if (event.keyCode == 13) {//user clicks Enter after typing a search word
        event.preventDefault();
        this.searchValues();
        return false;          
      }
    },
    
      
    searchValues : function(){

      this.clearResult();
        
      var query = this.searchField.val();
      if (query != '')
        this._load(query);
    },


    clearResult : function(){  
      this.resultSelect[0].options.length = 0;  
      this.notFoundMessage.hide();
      this.disableAddButton();              
    },


    _load : function(query){

      var widget = this;
      $.ajax({
          type: 'GET',
          url: this.ajaxUrl,
          async: true,
          data: {action:'ymm_restriction_search', search_query : query},
          dataType: 'json'
      }).success(
          function (data) {
            if (!data.error){                         
              widget.showResult(data);             
            }  
          }
        );      
    },

  
    showResult : function(options){
      var option, noSpaceRs;
    
      if (options.length){
    
        var l = options.length;

        var addedValues = [];
        
        var value = this.restrictionArea.val().split(' ').join('');        
        if (value){
          addedValues = value.split("\n");          
        }
         
        this.resultTitles = {};
        
        var ind = 0;    
        for (var i=0;i<l;i++){
          option = options[i];
          
          noSpaceRs = option.split(' ').join('');      
          if (addedValues.indexOf(noSpaceRs) != -1){//skip values that already exist in the restriction text area
            continue;
          }       
             
          this.resultSelect[0].options[ind] = new Option(option, option);
          this.resultSelect[0].options[ind].selected = true;
          this.resultTitles[option] = option;
          
          ind++;        
        }
        
        this.enableAddButton();      
      } else {   
        this.disableAddButton(); 
        this.notFoundMessage.show();      
      }       
    },

  
    disableAddButton : function(){
      this.resultSelect[0].disabled = true;
      this.addButton[0].disabled = true;      
      this.addButton.addClass('disabled');    	  	
    },
  
    enableAddButton : function(){
      this.resultSelect[0].disabled = false;    
      this.addButton[0].disabled = false;      
      this.addButton.removeClass('disabled');          	  	
    },

    
    addValue : function(){
      
      var values = [];
      
      var restriction = this.restrictionArea.val();        
      if (restriction){
        values = restriction.split("\n").filter(String);          
      }
                  
      var options = this.resultSelect.val();
      if (options){    
        var l = options.length;	  
        for (var i=0;i<l;i++){
          values.push(options[i]);
        }
        this.resultSelect.find("option:selected").remove();        
        values.sort();
      }

      this.restrictionArea.val(values.join("\n")).change();
    }
    
    	
  }); 
   
})(jQuery);
















