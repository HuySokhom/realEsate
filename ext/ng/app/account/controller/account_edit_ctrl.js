app.controller(
	'account_edit_ctrl', [
	'$scope'
	, 'Restful'
	, 'Services'
	, '$location'
	, 'Upload'
	, '$timeout'
	, function ($scope, Restful, Services, $location, Upload, $timeout){
		var url = 'api/Session/Customer/';
		$scope.service = new Services();
		// init tiny option
		$scope.tinymceOptions = {};
		$scope.init = function(params){
			Restful.get(url, params).success(function(data){
				$scope.user_name = data.elements[0].user_name;
				$scope.email = data.elements[0].customers_email_address;
				$scope.location = data.elements[0].customers_location;
				$scope.telephone = data.elements[0].customers_telephone;
				$scope.detail = data.elements[0].detail;
				$scope.address = data.elements[0].customers_address;
				$scope.photo_thumbnail = data.elements[0].photo_thumbnail;
				console.log(data);
			});
		};
		$scope.init();

		// update functionality
		$scope.save = function(){
			// set object to save into news
			var data = {
				user_name: $scope.user_name,
				customers_email_address: $scope.email,
				customers_location: $scope.location,
				customers_telephone: $scope.telephone,
				detail: $scope.detail,
				customers_address: $scope.address,
				photo: $scope.photo,
				photo_thumbnail: $scope.photo_thumbnail
			};
			console.log(data);
			$scope.disabled = false;

			Restful.put('api/Session/Customer/', data).success(function (data) {
				$scope.disabled = true;
				if(data == 1){
					$scope.service.alertMessage('Complete', 'Update Success.', 'success');
					$location.path('account');
				}else{
					$scope.service.alertMessage('Warning', 'Email Existing. Please use other email.', 'warning');
				}
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
						$scope.photo = response.data.image;
						$scope.photo_thumbnail = response.data.image_thumbnail;
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
	}
]);