app.controller(
	'property_edit_ctrl', [
	'$scope'
	, 'Restful'
	, '$stateParams'
	, 'Services'
	, '$location'
	, 'alertify'
	, 'Upload'
	, '$timeout'
	, function ($scope, Restful, $stateParams, Services, $location, $alertify, Upload, $timeout){
		// init tiny option
		$scope.tinymceOptions = {
			plugins: [
				"advlist autolink lists link image charmap print preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars fullscreen",
				"insertdatetime media nonbreaking save table contextmenu directionality",
				"emoticons template paste textcolor colorpicker textpattern media"
			],
			toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
			toolbar2: "print preview media | forecolor backcolor emoticons",
			image_advtab: true,
			paste_data_images: true
		};
		$scope.propertyTypes = ["For Sale", "For Rent", "Both Sale and Rent"];
		// init category
		$scope.initCategory = function(){
			Restful.get("api/Category").success(function(data){
				$scope.categoryList = data;
			});
			Restful.get("api/Location").success(function(data){
				$scope.provinces = data;
			});
		};
		$scope.initCategory();

		// functional for init district
		$scope.initDistrict = function(id){
			Restful.get("api/District/" + id).success(function(data){
				$scope.districts = data;
				$scope.communes = '';
			});
		};
		// functional for init Commune
		$scope.initCommune = function(id){
			Restful.get("api/Village/" + id).success(function(data){
				$scope.communes = data;
			});
		};
		var url = 'api/Session/User/ProductPost/';
		$scope.service = new Services();

		$scope.init = function(params){
			Restful.get(url + $stateParams.id, params).success(function(data){
				$scope.optionalImage = data.elements[0].image_detail;console.log(data);
				console.log($scope.optionalImage);
				$scope.news_type_id = data.elements[0].type[0].id;
				$scope.title_en = data.elements[0].detail[0].title;
				$scope.title_kh = data.elements[0].detail[1].title;
				$scope.content_en = data.elements[0].detail[0].content;
				$scope.content_kh = data.elements[0].detail[1].content;
				$scope.image = data.elements[0].image;
				$scope.image_thumbnail = data.elements[0].image_thumbnail;
			});
		};
		$scope.init();

		// update functionality
		$scope.save = function(){
			// set object to save into news
			var data = {
				products: {
					products_image: $scope.image,
					products_image_thumbnail: $scope.image_thumbnail,
					categories_id: $scope.categories_id,
					province_id: $scope.province_id,
					district_id: $scope.district_id,
					village_id: $scope.commune_id,
					products_price: $scope.price,
					products_kind_of: $scope.property_type,
					bed_rooms: $scope.bed_rooms,
					bath_rooms: $scope.bath_rooms,
					number_of_floors: $scope.number_of_floors,
				},
				products_description: [
					{
						products_name: $scope.title_en,
						products_description: $scope.content_en,
						language_id: 1
					},
					{
						products_name: $scope.title_kh,
						products_description: $scope.content_kh,
						language_id: 2
					}
				],
				products_image: $scope.optionalImage
			};
			$scope.disabled = false;

			Restful.put(url + $stateParams.id, data).success(function (data) {
				$scope.disabled = true;
				$scope.service.alertMessage('<b>Complete: </b>Update Success.');
				$location.path('manage_news');
			});
		};

		//functionality upload
		$scope.uploadPic = function(file) {
			if (file) {
				file.upload = Upload.upload({
					url: 'api/UploadImage',
					data: {file: file, username: $scope.username},
				});
				file.upload.then(function (response) {
					$timeout(function () {
						file.result = response.data;
						$scope.image = response.data.image;
						$scope.image_thumbnail = response.data.image_thumbnail;
					});
				}, function (response) {
					if (response.status > 0)
						$scope.errorMsg = response.status + ': ' + response.data;
				}, function (evt) {
					// Math.min is to fix I	E which reports 200% sometimes
					file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
				});
			}
		};

		// remove image
		$scope.removeImage = function ($index) {
			$alertify.okBtn("Ok")
				.cancelBtn("Cancel")
				.confirm(
					"<b>Waring: </b>" +
					"Are you sure want to delete this image." +
					"<br/>If you delete this image it will remove directly from database."
				, function (ev) {
					ev.preventDefault();
					$scope.optionalImage.splice($index, 1);
					//Restful.delete( url + id ).success(function(data){
					//	$scope.disabled = true;
					//	$scope.service.alertMessage('<strong>Complete: </strong>Delete Success.');
					//	$scope.category.elements.splice($index, 1);
					//});
				}, function(ev) {
					// The click event is in the
					// event variable, so you can use
					// it here.
					ev.preventDefault();
				});

		};

	}
]);