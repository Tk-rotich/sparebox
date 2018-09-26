( function ($) {
  "use strict";

  $.widget("pektsekye.ymm", { 
    
    rootCategoryIds: [],    
    selectedValues : [],
     
     
    _create : function () {

      $.extend(this, this.options);       

      this.garageContainer    = this.element.find('.ymm-garage');
      this.garageSelect       = this.element.find('.ymm-garage-select');        	
      this.extraContainer     = this.element.find('.ymm-extra');
      this.categoryContainer  = this.element.find('.ymm-category-container');    
      this.searchField        = this.element.find('.ymm-search-field');     
      this.searchAnySelButton = this.element.find('.ymm-submit-any-selection'); 
      
      
      this._on({ 
          "change .ymm-garage-select": $.proxy(this.preSelectDropdowns, this),        
          "click .ymm-remove-from-garage": $.proxy(this.garageRemove, this),      
          "change .ymm-select": $.proxy(this.loadLevel, this),
          "change .ymm-category-select": $.proxy(this.checkSubCategories, this),
          "submit form": $.proxy(this.submitSearch, this),                 
          "click button.ymm-submit-any-selection": $.proxy(this.submit, this),
          "click .ymm-clear-filter": $.proxy(this.clearFilter, this),  
          "click .ymm-search-all-link": $.proxy(this.searchAll, this)                                                                         
      }); 
                           
    },
  
  
    preSelectDropdowns : function(e){
    
      var vehicle = $(e.target).val();
      
      if (vehicle){
      
        this.selectedValues = vehicle.split(',');
      
        if (!this.canShowExtra){
      
          this._submit(null, null, this.selectedValues);

        } else {

          var firstValue = this.selectedValues[0];
          var firstSelect = this.element.find('.ymm-select').first();
      
          var option;
          var valueChanged = false;
          
          var l = firstSelect[0].options.length;
          for (var i=0;i<l;i++){
            option = firstSelect[0].options[i];
            if (option.value == firstValue){
              option.selected = true;
              valueChanged = true;
              break;
            }  
          }
          
          if (valueChanged){
            this.loadLevel({target:firstSelect});
          } else {// remove not found values
            this.garageRemove();
          }
        }
      }
      
      this.garageSetSelected(vehicle);            
    },  
  
  
    garageSetSelected : function(vehicle){ 
      var cookie = Cookies.get(this.partfinderCookieName);
      if (cookie){
        var selected = $.parseJSON(cookie);
        if (selected.vehicles && selected.vehicle != vehicle){
          selected.vehicle = vehicle;  
          Cookies.set(this.partfinderCookieName, JSON.stringify(selected)); 
        }                
      }    
    },  
  
  
    garageAdd : function(vehicle){
    
      var selected = {vehicle:vehicle, vehicles:[vehicle]};
    
      var cookie = Cookies.get(this.partfinderCookieName);
      if (cookie){
        var selectedOld;
        
      	try {
          selectedOld = $.parseJSON(cookie);                        		
        } catch (e){}
        
        if (selectedOld && selectedOld.vehicles && selectedOld.vehicles.length){ 
          selected.vehicles = selectedOld.vehicles;
          if (selectedOld.vehicles.indexOf(vehicle) == -1){
            if (selectedOld.vehicles.length > 9){ // limit garage to 10 values
              selected.vehicles.shift();
            }  
            selected.vehicles.push(vehicle);
            selected.vehicles.sort(this.sortCaseIns);
          }
        }           
      }    
      
      Cookies.set(this.partfinderCookieName, JSON.stringify(selected));         
    },  
  
  
    garageRemove : function(){
      var vehicle = this.garageSelect.val();
      if (vehicle == ''){
        return false;
      }  
      var cookie = Cookies.get(this.partfinderCookieName);
      if (cookie){
        var selected = $.parseJSON(cookie);
        if (selected.vehicles){
          this.without(selected.vehicles, vehicle);
          if (selected.vehicle == vehicle){
            selected.vehicle = selected.vehicles[0] ? selected.vehicles[0] : '';
          }
          
          this.garageSelect[0].remove(this.garageSelect[0].selectedIndex);       
                
          Cookies.set(this.partfinderCookieName, JSON.stringify(selected)); 
        }                
      }
      return false;    
    },


    clearFilter : function(){
      this.garageSetSelected('');
      return true;    
    },
    
    
    searchAll : function(){
    
      this.filterCategoryPage = 0;
      this.submitUrl = this.submitSearchUrl;
      this.canShowExtra = this.categorySearchEnabled || this.wordSearchEnabled;    
    
      var firstSelect = this.element.find('.ymm-select').first();
      
      this.disableLevels(firstSelect);
            
      firstSelect[0].length = 1;
        
      var l = this.firstLevelOptions.length;		  
      for (var i=0;i<l;i++){
        firstSelect[0].options[i+1] = new Option(this.firstLevelOptions[i], this.firstLevelOptions[i]);
      } 
      
      if (this.garageEnabled){          
        var cookie = Cookies.get(this.partfinderCookieName);
        if (cookie){
          var selected = $.parseJSON(cookie);
          if (selected.vehicles){
            var vehicle;
            var l = selected.vehicles.length;		  
            for (var i=0;i<l;i++){
              vehicle = selected.vehicles[i];
              this.garageSelect[0].options[i+1] = new Option(vehicle.split(',').join(' '), vehicle);
            }
            if (selected.vehicle){
              this.garageSelect.val(selected.vehicle).change();
            }
            this.garageContainer.show();          
          }                
        }       
      }
      
      var titleSpan = this.element.find('.ymm-title span');
      if (titleSpan.length){
        titleSpan.text(this.searchTitle);        
      } else {
        this.element.closest('div.widget').find('span.widget-title').text(this.searchTitle);
      }
      this.element.find('span.ymm-garage-text').text(this.garageText);
      this.searchAnySelButton.prop('title', this.searchButtonText).text(this.searchButtonText);
      this.element.find('span.ymm-filter-links').hide();
      
      return false;    
    },
    
    
    loadLevel : function(e){

      var element = $(e.target);    
      var value = element.val();
 
      this.disableLevels(element);
      this.hideExtra();
            
      if (value != ''){
    
        var values = [];
        var selects = this.element.find('.ymm-select');
        selects.each(function() {
          values.push($(this).val());                
          if (this == element[0])
            return false;  
        });         
              
        var nextLevel = values.length;
                 
        if (selects.length == values.length){// last drop-down is selected 
          if (this.canShowExtra)
            this.showExtra(values);
          else  
            this.submit();     
        } else {
          var categoryId = this.filterCategoryPage ? this.categoryId : 0;
          var widget = this;
          $.ajax({
              type: 'GET',
              url: this.ajaxShortUrl ? this.ajaxShortUrl : this.ajaxUrl,
              async: true,
              data: {action:'ymm_selector_fetch', cId:categoryId, 'values[]':values},
              dataType: 'json'
          }).success(
              function (data) {
                if (!data.error){           
                  if (data.length == 0){//there are no values for the next drop-down
                    widget.submit();
                  } else {                
                    widget.enableLevel(element, data, nextLevel);
                  }
                }  
              }
            );
        }  
      
      }
    
    },
  
  
    enableLevel : function(element, options, level){
    
      if (this.isHorizontal)
        var select = $(element).closest('.level').next().find('.ymm-select');
      else  
        var select = $(element).next('.ymm-select');
    
      var l = options.length;		  
      for (var i=0;i<l;i++)
        select[0].options[i+1] = new Option(options[i], options[i]);
      
      select[0].disabled = false; 
      select.removeClass('disabled');  
      
      if (this.garageEnabled){
        var selectedValue = this.selectedValues[level];
        if (selectedValue){
        
          var option;
          var valueChanged = false;
          
          var l = select[0].options.length;
          for (var i=0;i<l;i++){
            option = select[0].options[i];
            if (option.value == selectedValue){
              option.selected = true;
              valueChanged = true;
              break;
            }  
          }
          
          if (valueChanged){
            this.loadLevel({target:select});
          } else {// remove not found values
            this.garageRemove();
          } 
                
          this.selectedValues[level] = '';
        }
      }         
    },


    disableLevels : function(element){
      var disable = false;
      this.element.find('.ymm-select').each(function() {
        if (disable){
          this.length = 1;
          this.disabled = true;
          $(this).addClass('disabled');          
        }                  
        if (this == element[0])
          disable = true;  
      });   
    }, 
      

    showExtra : function(values){ 
  
      this.hideExtra(); 
             
      if (this.lastLevelIsSelected()){
        
        this.rootCategoryIds = [];
        this.categories = {}; 
          
        if (this.categorySearchEnabled){
          var jqxhr = this.loadCategoryDropdowns(values);
          if (jqxhr){
            jqxhr.always($.proxy(function(){           
              if (this.rootCategoryIds.length > 0){
                this.addCategorySelect(this.rootCategoryIds);
                if (this.wordSearchEnabled){
                  this.extraContainer.addClass('or-search');                  
                }                  
              }                
              this.extraContainer.show();
              if (this.wordSearchEnabled){              
                this.searchAnySelButton.hide();
              }                     
            },this));
          }       
        } else {
          this.extraContainer.show();
          if (this.wordSearchEnabled){          
            this.searchAnySelButton.hide();
          }
        }
            
      }	  
    },


    hideExtra : function(){
  
      if (this.categorySearchEnabled || this.wordSearchEnabled){
      
        this.extraContainer.hide();
      
        if (this.categorySearchEnabled)   
          this.removeSubCategories();
          
        if (this.wordSearchEnabled){
          this.extraContainer.removeClass('or-search');                  
          this.searchField.val('');         
        }                      
      }
         
      this.searchAnySelButton.show(); 
    },


    removeSubCategories : function(element){
           
      var isHorisontal = this.isHorizontal;            
           
      var startRemove = element == undefined ? true : false;
      this.element.find('.ymm-category-select').each(function() {
        if (startRemove){
          if (isHorisontal)
            $(this).closest('.level').remove();
          else
            $(this).remove();
        }                 
        if (!startRemove && this == element[0])
          startRemove = true;  
      });     
    },
 
 
    loadCategoryDropdowns : function(values){    
      var widget = this;
      var jqxhr = $.ajax({
          type: 'GET',
          url: this.ajaxShortUrl ? this.ajaxShortUrl : this.ajaxUrl,
          async: true,
          data: {action:'ymm_selector_get_categories', 'values[]':values},
          dataType: 'json'
      }).success(
          function (data) {
            if (!data.error && data.rootCategoryIds){            
              $.extend(widget, data);                         
            }  
          }
        );
        
      return jqxhr;              
    },

     
    addCategorySelect : function(categoryIds){
    
      var selectHtml = '<select class="ymm-category-select"></select>';

      if (this.isHorizontal){  
        selectHtml = '<div class="level">' +selectHtml+ '</div>';
        this.categoryContainer.find('.ymm-clear').before(selectHtml); 
      } else {
        this.categoryContainer.append(selectHtml);
      }     
         
      var select = this.element.find('.ymm-category-select').last();
      
      select[0].options[0] = new Option(this.categoryDefOptionTitle, '');
      
      var cId;  
      var l = categoryIds.length;		  
      for (var i=0;i<l;i++){
        cId = categoryIds[i];
        select[0].options[i+1] = new Option(this.categories[cId].title, cId);
      }    
    
    },


    checkSubCategories : function(e){
      var element = $(e.target)

      this.removeSubCategories(element);

      var cId = element.val();
      if (cId != ''){     
        if (this.categories[cId].children){
          this.addCategorySelect(this.categories[cId].children);
        } else {
          this.submitCategory(cId);
        }
      }        
    },  
      
      
    getLastSelectedCategory : function(){
    
      var categoryId = null;
      
      var widget = this;
      this.element.find('.ymm-category-select').each(function() {
        var cId = $(this).val();
        if (cId && widget.categories[cId].url){
          categoryId = cId;
        }   
      });
      
      return categoryId;
    },
    
          
    submit : function(){

      if (!this.firstLevelIsSelected()){
        return;
      }
      
      if (this.rootCategoryIds.length > 0){
        var categoryId = this.getLastSelectedCategory();
        if (categoryId){
          this.submitCategory(categoryId);
          return;
        }
      }        
        
      this._submit();
    },

    
    submitCategory : function(categoryId){
    
      var categoryUrl = this.categories[categoryId].url;

      this._submit(null, categoryUrl);
    },


    submitSearch : function(){

      var searchWord = this.searchField.val();         

      if (searchWord == '' && this.rootCategoryIds.length > 0){
        var categoryId = this.getLastSelectedCategory();
        if (categoryId){
          this.submitCategory(categoryId);
          return false;
        }
      }      
      
      this._submit(searchWord);    

      return false;
    },


    _submit : function(searchWord, categoryUrl, garageValues){

      if (!garageValues){
        if (this.lastLevelIsSelected()){
          var values = [];
          this.element.find('.ymm-select').each(function() {
            values.push($(this).val());                  
          });     
          this.garageAdd(values.join(','));
        } else {
          this.garageSetSelected('');
        }
      }
      
      var searchWord = searchWord ? searchWord : '';
      
      var params = (this.isCategoryPage && this.filterCategoryPage) || categoryUrl ? {} : {s:searchWord, ymm_search:1, post_type:'product'};
      
      var values = garageValues ? this.getValuesAsParams(garageValues) : this.getLevelValuesAsParams();
      $.extend(params, values);  
    
      var url = categoryUrl ? categoryUrl : this.submitUrl;
      
      window.location.href = url + '?' + $.param(params);
    },
    

    getLevelValuesAsParams : function(){  
  
      var params = {};
      var pNames = this.levelParameterNames;
      this.element.find('.ymm-select').each(function(i) {
        var v = $(this).val();
        if (v){
          params[pNames[i]] = v;
        }   
      });     
      
      return params;  	  
    },


    getValuesAsParams : function(garageValues){  
  
      var params = {};     
      var pNames = this.levelParameterNames;
      var l = garageValues.length;
      for (var i=0;i<l;i++) {
        params[pNames[i]] = garageValues[i];   
      }     
      
      return params;  	  
    },
    
    
    firstLevelIsSelected : function(){
      return this.element.find('.ymm-select').first().val() != '';
    },
    
   
    lastLevelIsSelected : function(){
      return this.element.find('.ymm-select').last().val() != '';
    },
    
    
    without : function(a, v){
      var i = a.indexOf(v);
      if (i != -1)
        a.splice(i, 1);
    },
    
    
    sortCaseIns : function(a, b){
      a = a.toLowerCase();
      b = b.toLowerCase();
      if (a == b) return 0;
      if (a > b) return 1;
    }	                  
    
            
  });
  
})(jQuery);            













