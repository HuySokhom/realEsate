<?php

use
	OSC\Product\Collection
		as ProductPostCol
	, OSC\Product\Object
		as ProductPostObj
	, OSC\ProductToCategory\Object
		as ProductToCategoryObj
	, OSC\ProductDescription\Object
		as ProductDescriptionObj
	, OSC\ProductContactPerson\Object
		as ProductContactPersonObj
	, OSC\ProductImage\Object
		as ProductImageObj
;

class RestApiSessionUserProductPost extends RestApi {

	public function get($params){
		$col = new ProductPostCol();
		$userId = $this->getOwner()->getId();
		if( !$userId ){
			throw new \Exception(
				"403: Access Denied",
				403
			);
		}else {
			$showDataPerPage = 10;
			$start = $params['GET']['start'] ? $params['GET']['start'] : 0;

			if($this->getId()){
				$col->sortByDate('DESC');
				$col->filterByCustomersId($userId);
				$params['GET']['id'] ? $col->filterById($params['GET']['id']) : '';
				$params['GET']['type'] ? $col->filterByCategoryId($params['GET']['type']) : '';
				$params['GET']['search_title'] ? $col->filterByTitle($params['GET']['search_title']) : '';
				// start limit page
				$showDataPerPage = 10;
				$start = $params['GET']['start'];
				$this->applyLimit($col,
						array(
								'limit' => array( $start, $showDataPerPage )
						)
				);
				$this->applyFilters($col, $params);
				$this->applySortBy($col, $params);
				return $this->getReturn($col, $params);
			}else{
				$where = "
					p.products_id = pd.products_id
						and
					p.categories_id = c.categories_id
						and
					c.language_id = '".$_SESSION['languages_id']."'
						and
					pd.language_id = '".$_SESSION['languages_id']."'
						and
					p.customers_id = '".$userId . "'
				";
				if($params['GET']['type']){
					$where .= "and p.categories_id = ".(int)$params['GET']['type']."";
				}
				if($params['GET']['search_title']){
					$where .= "and pd.products_name LIKE %" . $params['GET']['search_title'] ."%";
				}

				$news_query = tep_db_query("
					select
						p.products_id,
						p.products_price,
						p.products_promote,
						p.products_status,
						p.products_kind_of,
						p.bed_rooms,
						p.map_lat,
						p.map_long,
						p.bath_rooms,
						p.number_of_floors,
						p.province_id,
						p.village_id,
						p.district_id,
						c.categories_name,
						c.categories_id,
						pd.products_name,
						pd.products_description,
						pd.products_viewed,
						p.create_date
					from
						products p, products_description pd, categories_description c
					where
						" . $where . "
							order by
						p.create_date desc
						limit $start, $showDataPerPage
				");
				$array = array();
				while ($news_info = tep_db_fetch_array($news_query)){
					$array[] = $news_info;
				}
				$news_count = tep_db_query("select count(products_id) as total from products where customers_id = '" . $userId . "'");
				$total = tep_db_fetch_array($news_count);

				return array(
					data => array(
						elements => $array,
						count => $total['total']
					)
				);
			}
		}
	}

	public function post($params){
		$userId = $this->getOwner()->getId();
		if( !$userId ){
			throw new \Exception(
				"403: Access Denied",
				403
			);
		}else {
			$productObject = new ProductPostObj();
			$userId = $this->getOwner()->getId();
			$productObject->setCustomersId($userId);
			$productObject->setProductsPromote($_SESSION['customer_plan']);
			$productObject->setProperties($params['POST']['products']);
			$productObject->insert();
			$productId = $productObject->getProductsId();

			// save product to category
			$productToCategoryObject = new ProductToCategoryObj();
			$productToCategoryObject->setProductsId($productId);
			$productToCategoryObject->setCategoriesId($params['POST']['products']['categories_id']);
			$productToCategoryObject->insert();

			// save product images
			$productImageObject = new ProductImageObj();
			$fields = $params['POST']['products_image'];
			foreach ( $fields as $k => $v){
				$productImageObject->setProductsId($productId);
				$productImageObject->setProperties($v);
				$productImageObject->insert();
			}

			// save product description
			$fields = $params['POST']['products_description'];
			$productDetailObject = new ProductDescriptionObj();
			foreach ( $fields as $k => $v){
				$productDetailObject->setProductsId($productId);
				$productDetailObject->setProperties($v);
				$productDetailObject->insert();
			}
			unset($params);
			return array(
				'data' => array(
					'id' => $productId
				)
			);
		}
	}

	public function put($params){
		$userId = $this->getOwner()->getId();
		if( !$userId ){
			throw new \Exception(
				"403: Access Denied",
				403
			);
		}else {
			$cols = new ProductPostCol();
			$cols->filterByCustomersId($userId);
			$productId = $this->getId();
			$cols->filterById( $productId );
			if( $cols->getTotalCount() > 0 ){
				$cols->populate();
				$col = $cols->getFirstElement();
				$col->setProductsId($productId);
				$col->setProperties($params['PUT']['products']);
				$col->update();

				// update category to product
				$productToCategoryObject = new ProductToCategoryObj();
				$productToCategoryObject->setProductsId($productId);
				$productToCategoryObject->setCategoriesId($params['PUT']['products']['categories_id']);
				$productToCategoryObject->update();

				// save product description
				$fields = $params['PUT']['products_description'];
				$productDetailObject = new ProductDescriptionObj();
				foreach ( $fields as $k => $v){
					$productDetailObject->setProductsId($productId);
					$productDetailObject->setProperties($v);
					$productDetailObject->update();
					unset($v);
				}

				// update product image
				$imageFields = $params['PUT']['products_image'];
				$productImageObject = new ProductImageObj();
				$productImageObject->setProductsId($productId);
				// delete first insert new after
				$productImageObject->delete();
				foreach ( $imageFields as $k => $v){
					$productImageObject->setProductsId($productId);
					$productImageObject->setProperties($v);
					$productImageObject->insert();
					unset($v);
				}
				return array(
					'data' => array(
						'data' => 'update success'
					)
				);
			}
		}
	}

	public function patch($params){
		$userId = $this->getOwner()->getId();
		if( !$userId ){
			throw new \Exception(
				"403: Access Denied",
				403
			);
		}else {
			$cols = new ProductPostCol();
			$cols->filterByCustomersId($userId);
			$cols->filterById( $this->getId() );
			if( $cols->getTotalCount() > 0 ){
				$cols->populate();
				$col = $cols->getFirstElement();
				$col->setProductsId($this->getId());
				if( $params['PATCH']['name'] ){
					$col->setProductsStatus($params['PATCH']['status']);
					$col->updateStatus();
				}else{
					$col->refreshDate();
				}
			}
			return array(
				'data' => array(
					'data' => 'update success'
				)
			);
		}
	}
	public function delete($params){
		$userId = $this->getOwner()->getId();
		if( !$userId ){
			throw new \Exception(
				"403: Access Denied",
				403
			);
		}else {var_dump($params['DELETE']);
			if($params['DELETE']['image']){
				$image = new ProductImageObj();
				$image->setId($this->getId());
				$image->delete();
				return array(
					'data' => array(
						'data' => 'success'
					)
				);
			}else {
				$cols = new ProductPostCol();
				$cols->filterByCustomersId($userId);
				$cols->filterById($this->getId());
				if ($cols->getTotalCount() > 0) {
					$cols->populate();
					$col = $cols->getFirstElement();
					$col->setProductsId($this->getId());
					$col->delete();
				}
				return array(
					'data' => array(
						'data' => 'success'
					)
				);
			}
		}
	}

}

