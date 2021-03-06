app.controller(
    'report_customer_expire_ctrl', [
        '$scope'
        , 'Restful'
        , 'Services'
        , function ($scope, Restful, Services){
            $scope.service = new Services();
            $scope.headers = ['Customers Id', 'Customer Name', 'Plan', 'Type', 'Telephone', 'Email', 'Plan Start Date', 'Expire Date'];
            var url = 'api/ExpirePlan/';
            $scope.init = function(){
                $scope.csv = [];
                $scope.plans = '';
                Restful.get(url).success(function(data){
                    $scope.plans = data;
                    angular.forEach(data.elements, function(value, key) {
                        $scope.csv.push({
                            id: value.customers_id,
                            name: value.user_name,
                            plan: (value.plan == 3) ? 'Gold'
                                    : (value.plan == 2) ? 'Silver'
                                    : 'Standard',
                            type: value.user_type,
                            telephone: value.customers_telephone,
                            email: value.customers_email_address,
                            plan_date: formatDate(value.plan_date),
                            plan_expire: formatDate(value.plan_expire)
                        });
                    });console.log($scope.csv);
                });
            };
            $scope.init();
            $scope.print = function(){
                if( !$scope.plans ){
                    return $scope.service.alertMessage(
                        'Warning:','Please Filter to Print.','warning'
                    );
                }
                var divToPrint = document.getElementById("print");
                console.log(divToPrint);
                var newWin= window.open("");
                newWin.document.write('' +
                    '<html><head>' +
                    '<link href="css/print_table.css" rel="stylesheet" type="text/css">' +
                    '</head>' +
                    '<body>' + divToPrint.innerHTML + '</body>' +
                    '</html>'
                );
                newWin.print();
                newWin.close();
            };

            function formatDate(value){
                var date = new Date(value);
                return date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear();
            };

        }
    ]
);
