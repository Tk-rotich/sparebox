console.log("Loaded")
function populateModel(){
    var sparebox_cat_id = document.getElementById("sparebox_make_dropdown").value;
   jQuery.ajax({
		url : postModel.ajax_url,
		type : 'post',
		data : {
			action : 'post_sparebox_model',
			sparebox_cat_id : sparebox_cat_id
		},
		success : function( response ) {
           var select = document.getElementById("sparebox_model_dropdown");
           var select2 = document.getElementById("sparebox_engine_dropdown");
           var option = document.createElement("option");
           option.text = "2.5L";
           select.innerHTML = '';
            var res = JSON.parse(response)

            for (var i in res) {
                var optGroup = document.createElement('optgroup');
                optGroup.label = res[i].category;
                select.appendChild(optGroup);
                var opt = document.createElement('option');
                opt.value = res[i].sub_cat_id;
                opt.label = res[i].sub_category;
                select.appendChild(opt);
            }
            select.removeAttribute("disabled")
            select2.removeAttribute("disabled")
            select2.add(option)
		}
	});
}