app.controller(
	'customer_plan_ctrl', [
	'$scope'
	, 'Restful'
	, '$location'
	, 'Services'
	, 'alertify'
	, function ($scope, Restful, $location, Services, $alertify){
		$scope.service = new Services();
		var params = {pagination: 'yes'};
		var url = 'api/CustomerPlan/';
		$scope.init = function(params){
			Restful.get(url, params).success(function(data){
				$scope.plans = data;
				$scope.totalItems = data.count;
			});
		};
		$scope.init(params);

		$scope.updateStatus = function(params){
			params.status == 1 ? params.status = 0 : params.status = 1;
			var data = { status: params.status};
			Restful.put(url + params.id, data).success(function(data){
				console.log(data);
				$scope.service.alertMessage('<strong>Complete: </strong> Update Status Success.');
			});
		};

		$scope.save = function(){
			var data = {
				name: $scope.name
			};
			$scope.isDisabled = true;
			if( $scope.id ){
				Restful.put(url + $scope.id, data).success(function(data){
					$scope.init(params);
					$('#provincePopup').modal('hide');
					$scope.isDisabled = false;
					$scope.clear();
					$scope.service.alertMessage('<strong>Complete: </strong>Update Success.');
				});
			}else{
				Restful.post(url, data).success(function(data){
					$scope.init(params);
					$('#provincePopup').modal('hide');
					$scope.isDisabled = false;
					$scope.clear();
					$scope.service.alertMessage('<strong>Complete: </strong>Save Success.');
				});
			}
		};
		$scope.clear = function(){
			$scope.id = '';
			$scope.name = '';
		};
		// remove functionality
		$scope.remove = function(id, $index){
			$scope.id = id;
			$scope.index = $index;

			$alertify.okBtn("Ok")
				.cancelBtn("Cancel")
				.confirm("Are you sure want to delete this record?", function (ev) {
					// The click event is in the
					// event variable, so you can use
					// it here.
					ev.preventDefault();
					Restful.delete( url + $scope.id ).success(function(data){
						$scope.disabled = true;
						$scope.service.alertMessage('<strong>Complete: </strong>Delete Success.');
						$scope.init(params);
						//$scope.news.elements.splice($scope.index, 1);
						//$('#message').modal('hide');
					});
				}, function(ev) {
					// The click event is in the
					// event variable, so you can use
					// it here.
					ev.preventDefault();
				});

		};
		// search functionality
		$scope.search = function(){
			params.id = $scope.customer_id;
			$scope.init(params);
		};
		// edit functionality
		$scope.edit = function(params){
			$scope.name = params.name;
			$scope.id = params.id;
			$('#provincePopup').modal('show');
		};

		/**
		 * start functionality pagination
		 */
		$scope.currentPage = 1;
		//get another portions of data on page changed
		$scope.pageChanged = function() {
			$scope.pageSize = 10 * ( $scope.currentPage - 1 );
			params.start = $scope.pageSize;
			$scope.init(params);
		};
	}
]);
