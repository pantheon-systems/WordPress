<div id="bng-container">
	<h2>Import Locations</h2>
	<div class="dropzone" id="dropzone1">
		
	</div>
	<div class="container-button bng_block bng_center">
		<button class="bng_button_start bng_center" id="process_button"> Process File </button>
	</div>

</div>

<script type="text/javascript">
	var uploadFiles = [];

	jQuery(document).ready(function($){
		Dropzone.autoDiscover = false;

		$("#dropzone1").dropzone({ 
			url : '<?php echo admin_url( "admin-ajax.php" ); ?>',
			//uploadMultiple : true,
			acceptedFiles : '.xlsx',
			paramName : 'file',
			params : {action : "bng_upload_file" },
			addRemoveLinks : true,
			init : function(){
			   	this.on('success', function(file, response){
			   		response_obj = JSON.parse(response);
			   		
			   		if(response_obj.success){	
			   			uploadFiles.push({localName: file.name, serverName: response_obj.message});
			   		}
					
			   		
			   	});
			   	
			   	this.on('removedfile', function(file){
			   		var toDelete = '';
			   		$.each(uploadFiles, function(i, obj){
			   			if(obj.localName === file.name){
			   				toDelete = obj.serverName;
			   				return false;
			   			}
			   		
			   		});

			   		if(toDelete != ''){
			   			$.ajax({
			   				url : '<?php echo admin_url( "admin-ajax.php" ); ?>',
			   				type : 'POST',
			   				data : 'file='+toDelete+"&action=bng_remove_file",
			   				
			   			}).done(function(rpta){
							console.log(rpta);
							var index = -1;
							$.each(uploadFiles, function(i, obj){
					   			if(obj.serverName === toDelete){
					   				index = i;
					   				return false;
					   			}
					   		});

							if(index > -1)
								uploadFiles.splice(index, 1);

			   				}).error(function(err){
			   					console.log(err);
			   				});
			   			}
			   		});
			   	},
			   	
		});
			 

		$(document).on('click', '#process_button', function(e){
			e.preventDefault();
			var files = '';
			var files_arr = [] ;
			if(uploadFiles.length > 0){
				$.each(uploadFiles, function(i, nfile){
					files_arr.push(nfile.serverName);
				});
				console.log(files_arr);
				files = files_arr.join();
			}

			var data = 'files='+files+'&action=bng_process_files';

			$.ajax({
				beforeSend: function(){
					$.LoadingOverlay("show",{
						image : "<?php echo plugins_url('/assets/img/loading.gif', __DIR__); ?>"
					});
				},
				url : '<?php echo admin_url( "admin-ajax.php" ); ?>',
				type : 'post',
				data : data,
				dataType : 'json'
			}).done(function(response){
				$.LoadingOverlay("hide");

				var n = noty({
				    text: response.message,
				    animation: {
				        open: {height: 'toggle'},
				        close: {height: 'toggle'},
				        easing: 'swing',
				        speed: 500
				    }
				});

			}).fail(function(error){
				$.LoadingOverlay("hide");
				var n = noty({
				    text: error.responseText,
				    animation: {
				        open: {height: 'toggle'},
				        close: {height: 'toggle'},
				        easing: 'swing',
				        speed: 500
				    }
				});
			});

		});		   	

	});
</script>