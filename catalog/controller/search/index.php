<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerSearchIndex extends Controller {
    private $_pageInfo = array();
    private $_langPrefix = '';
    
	public function index() {
//        echo '<pre>';
//        print_r($this->request); die;
//        echo '</pre>';
        
//        var_dump($this->config->get('config_language_id'));
        
        // модель tecdoc работает с объектом подключения tecdoc
        $tecdocDb = new DB(DB_DRIVER_TECDOC, DB_HOSTNAME_TECDOC, DB_USERNAME_TECDOC, DB_PASSWORD_TECDOC, DB_DATABASE_TECDOC);
        $this->registry->set('tecdoc', $tecdocDb);
        
		$this->load->language('search/index');
//
		$this->load->model('search/tecdoc');
        
        // подпорка для работы с языком, но в целом данный подход совсем не правильный
        if (!empty($this->request->get['lang'])) {
            $this->_langPrefix = '/'.$this->request->get['lang'];
        }
        
        // эмуляция режимов
//        $mode = !empty($this->request->get['mode']) ? $this->request->get['mode'] : 'brands';
//        
//        if ($mode == 'models') {
//            $brandRequestParam = 'bmw_16';
//        }
//        if ($mode == 'modifications') {
//            $brandRequestParam = 'bmw_16';
//            $modelRequestParam = 'x5-f15-f85_11482';
//        }
        
        // рабочее определение режимов. SEO-url парсится в controllers/startup/seo_url и 
        // формирует в request массив нужных параметров: brand, model
        //$mode = 'types';
        $mode = 'brands';
        $data['page_url'] = '/mark';
        
        // от этого шага решили отказаться
//        if (!empty($this->request->get['type'])) {
//            $mode = 'brands';
//            $typeRequestParam = !empty($this->request->get['type']) ? $this->request->get['type'] : '';
//            $data['page_url'] .= '/'.$typeRequestParam;
//        }
        $typeRequestParam = '';
        $brandRequestParam = '';
        $modelRequestParam = '';
        $modificationRequestParam = '';
        
        if (!empty($this->request->get['brand'])) {
            $mode = 'models';
            $brandRequestParam = !empty($this->request->get['brand']) ? $this->request->get['brand'] : '';
            $data['page_url'] .= '/'.$brandRequestParam;
        }
        if (!empty($this->request->get['model'])) {
            $mode = 'modifications';
            $modelRequestParam = !empty($this->request->get['model']) ? $this->request->get['model'] : '';
            $data['page_url'] .= '/'.$modelRequestParam;
        }
        if (!empty($this->request->get['modification'])) {
            $mode = 'products';
            $modificationRequestParam = !empty($this->request->get['modification']) ? $this->request->get['modification'] : '';
            $categoryRequestParam = !empty($this->request->get['category']) ? (int)$this->request->get['category'] : '';
            $data['page_url'] .= '/'.$modificationRequestParam;
        }
        
$log = fopen(DIR_LOGS . 'db.log', 'a');
fwrite($log, '***********' . $mode. date('Y-m-d H:i:s'). '***************'. chr(13) . chr(13));
fclose($log);
        
//		$data['breadcrumbs'] = array();
//		$data['breadcrumbs'][] = array(
//			'text' => $this->language->get('text_home'),
//			'href' => $this->_langPrefix.$this->url->link('common/home')
//		);
        // получаем Meta-информацию о странице и возможно текстовый контент
        $this->getPageInformation('mark_'.$mode, $data['page_url']);
        
        $data['page_url'] = $this->_langPrefix.$data['page_url'];
        
        switch ($mode) {
//            case 'types':
//                $data = $this->getTypesData($data);
//                $template = 'search/types';
//                break;
        
            case 'brands':
                $data = $this->getBrandsData($data, $typeRequestParam);
                $template = 'search/brands';
                break;

            case 'models':
                $data = $this->getModelsData($data, $typeRequestParam, $brandRequestParam);
                $template = 'search/models';
                break;

            case 'modifications':
                $data = $this->getModificationsData($data, $typeRequestParam, $brandRequestParam, $modelRequestParam);
                $template = 'search/modifications';
                break;
            
            case 'products':
                $data = $this->getProductsData($data, $typeRequestParam, $brandRequestParam, $modelRequestParam, $modificationRequestParam, $categoryRequestParam);
                $template = 'search/products';
                break;
        }
        
        //if (!array_key_exists('breadcrumbs', $data)) {
        if (empty($data)) {
            $this->gotoError();
            
        } else {
        
//			$this->document->setDescription($information_info['meta_description']);
//			$this->document->setKeywords($information_info['meta_keyword']);
//
//			$data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
//
//			$data['continue'] = $this->url->link('common/home');
//            
//            if ($information_id == 15 || $information_id == 16) {
//                $data['contact_form_type'] = 'repaire';
//                $this->load->language('mail/contact_'.$data['contact_form_type']);
//            }
            
            if (!empty($this->_pageInfo['description'])) {
                $data['page_description'] =  html_entity_decode($this->_pageInfo['description'], ENT_QUOTES, 'UTF-8');
            }

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view($template, $data));
        }
	}
    
//    protected function getTypesData($data) {
//        $data['breadcrumbs'][] = array(
//            'text' => $this->language->get('types'),
////            'href' => $this->url->link('information/information', 'information_id=' .  $information_id)
//        );
//        $this->document->setTitle($this->language->get('title_prefix'). $this->language->get('types'));
//        
//        $data['heading_title'] = $this->language->get('types');
//        $data['helper'] = $this->language->get('types_help');
//        
//        $types = $this->model_search_tecdoc->getTypes();
//        
//        foreach ($types as $type) {
//			$data['types'][] = array(
//				'name' => $this->language->get($type),
//				'href' => '/mark/'. $type//$this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
//			);
//		}
//
//        return $data;
//    }
    
    protected function getBrandsData($data, $typeRequestParam) {
        $data['breadcrumbs'] = $this->getBreadcrumbs('brands');

        $this->setPageMetaInformation(
            [
                'meta_title' => $this->language->get('title_prefix'). $this->language->get('brands'),
                'meta_description' => 'Каталог стартеров и генераторов оптом и в розницу. Продажа и ремонт запчастей для стартера и генератора автомобиля в Харькове Днепропетровске Одессе и Киеве с доставкой по всей Украине.',
            ],
            []
        );
        
        $data['heading_title'] = $this->language->get('brands');
//        $data['heading_title'] = $this->language->get('brands').' ('.$this->language->get($typeRequestParam).')';
        $data['helper'] = $this->language->get('brands_help');
        $data['index_title'] = $this->language->get('index_title');
        
//        $this->model_search_tecdoc->setType($typeRequestParam);
        $brands = $this->model_search_tecdoc->getBrands($typeRequestParam);
        
        foreach ($brands as $brand) {
			if (is_numeric(utf8_substr($brand['name'], 0, 1))) {
				$key = '0 - 9';
			} else {
				$key = utf8_substr(utf8_strtoupper($brand['name']), 0, 1);
			}

			if (!isset($data['brand_categories'][$key])) {
				$data['brand_categories'][$key]['name'] = $key;
			}

			$data['brand_categories'][$key]['manufacturer'][] = array(
				'name' => $brand['name'],
                'href' => $data['page_url']. '/'.$this->getLink($brand['name'], $brand['id'])
				//'href' => '/mark/'.$typeRequestParam.'/'. $this->getLink($brand['name'], $brand['id'])//$this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
			);
		}

        return $data;
    }

    protected function getModelsData($data, $typeRequestParam, $brandRequestParam) {
        $brandRequest = explode('_', $brandRequestParam);
        
        if (empty($brandRequest[1])) { //empty($typeRequestParam) || 
            return array();
        }
        
        // получаем информацию по брэнду
        $brandId = (int) $brandRequest[1];
//        $this->model_search_tecdoc->setType($typeRequestParam);
        $brand = $this->model_search_tecdoc->getBrandById($brandId);
        
        if (empty($brand['name'])) {
            return array();
        }
        
        $data['heading_title'] = $brand['name'];
        $data['helper'] = $this->language->get('models_help');
        
        $data['breadcrumbs'] = $this->getBreadcrumbs('models', [], [
            'brandName' => $brand['name']
        ]);

        $this->setPageMetaInformation(
            [
                'meta_title' => $this->language->get('title_prefix'). $brand['name'],
                'meta_description' => 'Каталог стартеров и генераторов на {manufacturer} оптом и в розницу. Продажа и ремонт запчастей для стартера и генератора автомобиля в Харькове Днепропетровске Одессе и Киеве с доставкой по всей Украине.',
            ],
            [
                'manufacturer' => $brand['name'],
            ]
        );
        
        // получаем список моделей
        $models = $this->model_search_tecdoc->getModels($typeRequestParam, $brandId);
        
//        echo '<pre>';
//        var_dump($models);
        
        // схлопываем модели в списке в группу укрупненной модели
        $modelGroups = array();
        
        foreach ($models as $model) {
            $modelGroupName = (!empty($model['model_group']) ? $model['model_group'] : $model['name']);
            $modelGroupUrl = (!empty($model['model_group_url']) ? 
                    strtolower($model['model_group_url']) : 
                    $this->getLink($model['name'], $model['id']));
            
            $modelGroups[$modelGroupName] = [
                'url' => $modelGroupUrl,
                'models' => array(),
            ];
            //$modelGroups[$modelGroupName]['models'][] = $model;
        }
        
        foreach ($modelGroups as $modelGroupName=>$modelGroup) {
			if (is_numeric(utf8_substr($modelGroupName, 0, 1))) {
				$key = '0 - 9';
			} else {
				$key = utf8_substr(utf8_strtoupper($modelGroupName), 0, 1);
			}

			if (!isset($data['model_categories'][$key])) {
				$data['model_categories'][$key]['name'] = $key;
			}
            
//            $groupModelId = '';
//            $groupMinYear = '';
//            $groupMaxYear = '';
            
//            echo '<br>'.$groupName.':';
//            foreach($models as $model) {
//                echo $model['id'].',';
                //$groupModelId .= (!empty($groupModelId)? '-':''). $model['id'];
//                $years = explode('-', $model['constructioninterval']);
//                
//                if (!empty($years[0])) {
//                    $minYear = substr(trim($years[0]), 3);
//                    
//                    if ($groupMinYear === '' || $minYear > $groupMinYear) {
//                        $groupMinYear = $minYear;
//                    }
//                    $maxYear = ' - '. (!empty(trim($years[1])) ? trim($years[1]) : '...');
//                }
//                if (!empty($years[1])) {
//                    $maxYear = trim($years[1]);
//                }
//            }

			$data['model_categories'][$key]['model'][] = array(
				'name' => $modelGroupName,
                //'sub' => $groupMinYear. $groupMaxYear,
                'href' => $data['page_url']. '/'.$modelGroup['url']
			);
//            $data['model_categories'][$key]['model'][] = array(
//				'name' => $model['name'],
//                'sub' => $model['constructioninterval'],
//				'href' => '/mark/'.$typeRequestParam.'/'.$brandRequestParam. '/'.$this->getLink($model['name'], $model['id'])//$this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
//			);
		}
        
        return $data;
    }
    
    protected function getModificationsData($data, $typeRequestParam, $brandRequestParam, $modelRequestParam) {
        $brandRequest = explode('_', $brandRequestParam);
        $modelRequest = explode('_', $modelRequestParam);
                
        if (empty($brandRequest[1])) { //empty($typeRequestParam) ||  || empty($modelRequest[1])
            return array();
        }
        
        // получаем информацию по брэнду
        $brandId = (int) $brandRequest[1];
//        $this->model_search_tecdoc->setType($typeRequestParam);
        $brand = $this->model_search_tecdoc->getBrandById($brandId);
        
        if (empty($brand['name'])) {
            return array();
        }
        
        // получаем информацию по модели
//        $modelId = (int) $modelRequest[1];
//        $model = $this->model_search_tecdoc->getModelById($modelId);
//        
//        if (empty($model['name'])) {
//            return array();
//        }
        
        // поскольку теперь у нас в виде модели выступает обобщенная группа, мы делаем выборку моделей по указанной группе
        $models = $this->model_search_tecdoc->getModelsByGroup($modelRequestParam, (!empty($modelRequest[1]) ? $modelRequest[1] : 0));
        $modelIds = array();
        $modelGroupName = '';
        $modelGroupUrl = '';
        $modelType = 'passenger';
        
        foreach($models as $model) {
            $modelIds[] = $model['id'];
            
            $modelGroupName = (!empty($model['model_group']) ? $model['model_group'] : $model['name']);
            $modelGroupUrl = (!empty($model['model_group_url']) ? $model['model_group_url'] : $this->getLink($model['name'], $model['id']));
            
            // определяем тип модели неявно!, просто по признакам у всех моделей в группе, чтобы сделать выборку из нужной таблицы текдока
            if ($model['iscommercialvehicle']=='True') {
                $modelType = 'commercial';
            }
            if ($model['ismotorbike']=='True') {
                $modelType = 'motorbike';
            }   
        }
//        var_dump($modelGroupUrl);

        $data['type'] = $typeRequestParam;
        $data['heading_title'] = $brand['name'].' '.$modelGroupName;
        //$data['heading_title'] = $brand['name'].' '.$model['name'];
        $data['helper'] = $this->language->get('modifications_help');
                    
        // формируем хлебные крошки
        $data['breadcrumbs'] = $this->getBreadcrumbs('modifications', [
            'brandRequestParam' => $brandRequestParam,
        ], [
            'brandName' => $brand['name'],
            'modelGroupName' => $modelGroupName,
        ]);
//        $data['breadcrumbs'][] = array(
//            'text' => $this->language->get('types'),
//            'href' => '/mark'
//        );
//        $data['breadcrumbs'][] = array(
//            'text' => $this->language->get('brands'),
//            'href' => '/mark'
//            //'text' => $this->language->get('brands').' ('.$this->language->get($typeRequestParam).')',
//            //'href' => '/mark/'.$typeRequestParam
//        );
//        $data['breadcrumbs'][] = array(
//            'text' => $brand['name'],
//            'href' => '/mark/'.$brandRequestParam
//            //'href' => '/mark/'.$typeRequestParam.'/'.$brandRequestParam
//        );
//        $data['breadcrumbs'][] = array(
//            'text' => $modelGroupName, //$model['name'],
//        );
        $this->setPageMetaInformation(
            [
                'meta_title' => $this->language->get('title_prefix'). $brand['name'].' '.$modelGroupName,
                'meta_description' => 'Каталог стартеров и генераторов на {manufacturer} {model} оптом и в розницу. Продажа и ремонт запчастей для стартера и генератора автомобиля в Харькове Днепропетровске Одессе и Киеве с доставкой по всей Украине.',
            ],
            [
                'manufacturer' => $brand['name'],
                'model' => $modelGroupName,
            ]
        );
        //$this->document->setTitle($this->language->get('title_prefix'). $brand['name'].' '.$model['name']);
        
        // получаем список модификаций для данной модели
        //$mods = $this->model_search_tecdoc->getModifications($typeRequestParam, $modelId);
        $mods = $this->model_search_tecdoc->getModifications($modelType, [
            'models' => $modelIds,
//            'modelId' => $modelId,
        ]);
        
//                echo '<pre>';
//        print_r($mods); die;
        
        foreach ($mods as $modification) {
			$data['modifications'][] = array(
                'model' => $modification['model_name'],
				'modification' => $modification['description'],
                'year' => $modification['constructioninterval'],
                'engine' => $modification['engine_name'],
                'href' => $data['page_url']. '/'.$this->getLink($modification['description'], $modification['id']),
				//'href' => '/mark/'.$brandRequestParam. '/'. $modelRequestParam.'/'. $this->getLink($modification['description'], $modification['id'])//.$typeRequestParam.'/'
			);
		}
        
        return $data;
    }
    
    protected function getProductsData($data, $typeRequestParam, $brandRequestParam, $modelRequestParam, $modificationRequestParam, $categoryRequestParam) {
if(catalog_controller_search_index__getProductsData){
$log = fopen(DIR_LOGS . 'catalog_controller_search_index__getProductsData.log', 'w');
fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fwrite($log,' $typeRequestParam )' . print_r($typeRequestParam, true) . '==' . chr(10) . chr(13));
fwrite($log,' $brandRequestParam )' . print_r($brandRequestParam, true) . '==' . chr(10) . chr(13));
fwrite($log,' $modelRequestParam )' . print_r($modelRequestParam, true) . '==' . chr(10) . chr(13));
fwrite($log,' $modificationRequestParam )' . print_r($modificationRequestParam, true) . '==' . chr(10) . chr(13));
fwrite($log,' $categoryRequestParam )' . print_r($categoryRequestParam, true) . '==' . chr(10) . chr(13));
fclose($log);}
        $brandRequest = explode('_', $brandRequestParam);
        $modelRequest = explode('_', $modelRequestParam);
        $modificationRequest = explode('_', $modificationRequestParam);
if(catalog_controller_search_index__getProductsData){
$log = fopen(DIR_LOGS . 'catalog_controller_search_index__getProductsData.log', 'a');
fwrite($log,' =================' . '==' . chr(10) . chr(13));
fwrite($log,' $brandRequest )' . print_r($brandRequest, true) . '==' . chr(10) . chr(13));
fwrite($log,' $modelRequest )' . print_r($modelRequest, true) . '==' . chr(10) . chr(13));
fwrite($log,' $modificationRequest )' . print_r($modificationRequest, true) . '==' . chr(10) . chr(13));
fclose($log);}
        
        if (empty($brandRequest[1]) || empty($modificationRequest[1])) {
            //empty($typeRequestParam) || empty($modelRequest[1]) || 
            return array();
        }
        
        // получаем информацию по брэнду
        $brandId = (int) $brandRequest[1];        
        $brand = $this->model_search_tecdoc->getBrandById($brandId);
if(catalog_controller_search_index__getProductsData){
$log = fopen(DIR_LOGS . 'catalog_controller_search_index__getProductsData.log', 'a');
fwrite($log,' =================' . '==' . chr(10) . chr(13));
fwrite($log,' $brandId )' . print_r($brandId, true) . '==' . chr(10) . chr(13));
fwrite($log,' $brand = $this->model_search_tecdoc->getBrandById($brandId); )' . print_r($brand, true) . '==' . chr(10) . chr(13));
fclose($log);}
        
        if (empty($brand['name'])) {
            return array();
        }
        
        // получаем информацию по модели
//        $modelId = (int) $modelRequest[1];  
//        $model = $this->model_search_tecdoc->getModelById($modelId);
//        
//        if (empty($model['name'])) {
//            return array();
//        }
        // поскольку теперь у нас в виде модели выступает обобщенная группа, мы делаем выборку моделей по указанной группе
        $models = $this->model_search_tecdoc->getModelsByGroup($modelRequestParam, (!empty($modelRequest[1]) ? $modelRequest[1] : 0));
        $modelIds = array();
        $modelGroupName = '';
        $modelGroupUrl = '';
        $modelType = 'passenger';
if(catalog_controller_search_index__getProductsData){
$log = fopen(DIR_LOGS . 'catalog_controller_search_index__getProductsData.log', 'a');
fwrite($log,' =================' . '==' . chr(10) . chr(13));
fwrite($log,' $models = $this->model_search_tecdoc->getModelsByGroup($modelRequestParam, (!empty($modelRequest[1]) ? $modelRequest[1] : 0)); )' . print_r($models, true) . '==' . chr(10) . chr(13));
fclose($log);}
        
        foreach($models as $model) {
            $modelIds[] = $model['id'];
            
            $modelGroupName = (!empty($model['model_group']) ? $model['model_group'] : $model['name']);
            $modelGroupUrl = (!empty($model['model_group_url']) ? $model['model_group_url'] : '');
            
            // определяем тип модели неявно!, просто по признакам у всех моделей в группе, чтобы сделать выборку из нужной таблицы текдока
            if ($model['iscommercialvehicle']=='True') {
                $modelType = 'commercial';
            }
            if ($model['ismotorbike']=='True') {
                $modelType = 'motorbike';
            }   
        }
        
        // получаем информацию по модификации
        $modificationId = (int) $modificationRequest[1];
        $modification = $this->model_search_tecdoc->getModificationById($modificationId, $modelType);
if(catalog_controller_search_index__getProductsData){
$log = fopen(DIR_LOGS . 'catalog_controller_search_index__getProductsData.log', 'a');
fwrite($log,' =================' . '==' . chr(10) . chr(13));
fwrite($log,' $modificationId )' . print_r($modificationId, true) . '==' . chr(10) . chr(13));
fwrite($log,' $modelType )' . print_r($modelType, true) . '==' . chr(10) . chr(13));
fwrite($log,' $modification = $this->model_search_tecdoc->getModificationById($modificationId, $modelType); )' . print_r($modification, true) . '==' . chr(10) . chr(13));
fclose($log);}
        
        if (empty($modification['name'])) {
            return array();
        }
        
        $tecdocCategoryId = (!empty($categoryRequestParam) ? (int)$categoryRequestParam : '');
        
//        var_dump($modelGroupName); 
        
        $data['type'] = $typeRequestParam;
        //$data['heading_title'] = $brand['name'].' '.$model['name'].' '.$modification['name'];
        $data['heading_title'] = $brand['name'].' '.$modelGroupName.' '.$modification['name'];
        $data['helper'] = $this->language->get('modifications_help');
                    
        // формируем хлебные крошки
        $data['breadcrumbs'] = $this->getBreadcrumbs('products', [
            'brandRequestParam' => $brandRequestParam,
            'modelRequestParam' => $modelRequestParam,
        ], [
            'brandName' => $brand['name'],
            'modelGroupName' => $modelGroupName,
            'modificationName' => $modification['name'],
        ]);
//        $data['breadcrumbs'][] = array(
//            'text' => $this->language->get('types'),
//            'href' => '/mark'
//        );
//        $data['breadcrumbs'][] = array(
//            'text' => $this->language->get('brands'),
//            'href' => '/mark'
//            //'text' => $this->language->get('brands').' ('.$this->language->get($typeRequestParam).')',
//            //'href' => '/mark/'.$typeRequestParam
//        );
//        $data['breadcrumbs'][] = array(
//            'text' => $brand['name'],
//            'href' => '/mark/'.$brandRequestParam
//            //'href' => '/mark/'.$typeRequestParam.'/'.$brandRequestParam
//        );
//        $data['breadcrumbs'][] = array(
//            'text' => $modelGroupName,//$model['name'],
//            'href' => '/mark/'.$brandRequestParam.'/'.$modelRequestParam
//            //'href' => '/mark/'.$typeRequestParam.'/'.$brandRequestParam.'/'.$modelRequestParam
//        );
//        $data['breadcrumbs'][] = array(
//            'text' => $modification['name'],
//        );
        
        $this->setPageMetaInformation(
            [
                'meta_title' => $this->language->get('title_prefix'). $brand['name'].' '.$modelGroupName.' '.$modification['name'],
                'meta_description' => 'Каталог стартеров и генераторов на {manufacturer} {model} {modification} оптом и в розницу. Продажа и ремонт запчастей для стартера и генератора автомобиля в Харькове Днепропетровске Одессе и Киеве с доставкой по всей Украине.',
            ],
            [
                'manufacturer' => $brand['name'],
                'model' => $modelGroupName,
                'modification' => $modification['name'],
            ]
        );
        
        // ниже код частично заимствован из controller/category/index ---------------------
        $this->load->language('product/category');
//		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
        
        $params = array(
            'filter' => [
                'value' => '',
                'default' => ''
            ],
            'sort' => [
                'value' => '',
                'default' => 'p.sort_order'
            ],
            'order' => [
                'value' => '',
                'default' => 'ASC'
            ],
            'page' => [
                'value' => '',
                'default' => 1
            ],
            'limit' => [
                'value' => '',
                'default' => 1000, //$this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit')
            ],
        );
if(catalog_controller_search_index__getProductsData){
$log = fopen(DIR_LOGS . 'catalog_controller_search_index__getProductsData.log', 'a');
fwrite($log,' =================' . '==' . chr(10) . chr(13));
fwrite($log,' $params )' . print_r($params, true) . '==' . chr(10) . chr(13));
fclose($log);}        
//        $isSetRobots = false;
        foreach($params as $paramName=>$paramArr) {
            if (isset($this->request->get[$paramName])) {
                $params[$paramName]['value'] = $this->request->get[$paramName];
                
//                if (!$isSetRobots) {
//                    $this->document->setRobots('noindex,follow');
//                    $isSetRobots = true;
//                }
            }
            
            // объявляем переменные $filter, $sort, $order, $page, $limit
            $paramName = (isset($this->request->get[$paramName]) ? $this->request->get[$paramName] : $params[$paramName]['default']);
        }
if(catalog_controller_search_index__getProductsData){
$log = fopen(DIR_LOGS . 'catalog_controller_search_index__getProductsData.log', 'a');
fwrite($log,' =================' . '==' . chr(10) . chr(13));
fwrite($log,' if (isset($this->request->get[$paramName])) { $params[$paramName][value] = $this->request->get[$paramName];  $params )' . print_r($params, true) . '==' . chr(10) . chr(13));
fclose($log);}        

       $url = '';
       
		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
			$this->document->setRobots('noindex,follow');
           
           $url .= '&filter=' . $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
			$this->document->setRobots('noindex,follow');
           
           $url .= '&sort=' . $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
			$this->document->setRobots('noindex,follow');
           
           $url .= '&order=' . $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
			$this->document->setRobots('noindex,follow');
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
			$this->document->setRobots('noindex,follow');
           
           $url .= '&limit=' . $this->request->get['limit'];
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}
        
        $data['text_empty'] = $this->language->get('text_empty');
//			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
//			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
//			$data['compare'] = $this->url->link('product/compare');
        
        
        
        $menu = $this->load->controller('tecdoc/category', array(
            'modificationId' => $modificationId,
            'type' => $modelType,
            //'type' => $typeRequestParam,
            'path' => $data['page_url'],
            //'path' => '/mark/'.$brandRequestParam.'/'.$modelRequestParam.'/'.$modificationRequestParam,
            //'path' => '/mark/'.$typeRequestParam.'/'.$brandRequestParam.'/'.$modelRequestParam.'/'.$modificationRequestParam,
            'tecdocCategoryId' => $tecdocCategoryId,
        ));
        $menu = trim($menu);
        
        if (!empty($menu)) {
            $data['column_menu'] = $menu;
        }

        if (empty($tecdocCategoryId)) {
            $data['products_init'] = $this->language->get('products_init');
            $product_total = 0;
			$results = [];
            
        } else {
            $data['tecdocCategoryId'] = $tecdocCategoryId;
            $data['page_url'] .= '/'.$tecdocCategoryId;
            
            // формируем в текдок-базе список подходящих запчастей для указанной модели
//            $data['tecdocProducts'] = $this->model_search_tecdoc->getProducts($modelType, $modificationId, $tecdocCategoryId, $brandId);
            $this->model_search_tecdoc->getProducts($modelType, $modificationId, $tecdocCategoryId, $brandId);

            $data['products'] = array();

			$filter_data = array(
//				'filter_category_id' => $category_id,
                'filter_tecdoc' => true,
				//'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit,
			);

			//$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
      $product_total = $this->model_catalog_product->getTotalProducts_ForSessionSearch($filter_data);
			$results = $this->model_catalog_product->getProducts($filter_data);
if(catalog_controller_search_index__getProductsData){
$log = fopen(DIR_LOGS . 'catalog_controller_search_index__getProductsData.log', 'a');
fwrite($log,' =================' . '==' . chr(10) . chr(13));
fwrite($log,' $filter_data )' . print_r($filter_data, true) . '==' . chr(10) . chr(13));
fwrite($log,' $product_total = $this->model_catalog_product->getTotalProducts_ForSessionSearch($filter_data); )' . print_r($product_total, true) . '==' . chr(10) . chr(13));
fwrite($log,' $results = $this->model_catalog_product->getProducts($filter_data); )' . print_r($results, true) . '==' . chr(10) . chr(13));
fclose($log);}        

            
//                $log = fopen(DIR_LOGS . 'catalog_controller_product_category__index.log', 'w');
//                fwrite($log,' ' . gmdate('d.m.Y  H:i:s') . '==' . chr(10) . chr(13));
//                fwrite($log,' $filter_data )' . print_r($filter_data, true) . '==' . chr(10) . chr(13));
//                fwrite($log,' $results )' . print_r($results, true) . '==' . chr(10) . chr(13));
//                fclose($log);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

                if ($result['quantity'] <= 0  || empty($result['quantity']) ) {
                    $stock = $result['stock_status'];
                } elseif ($this->config->get('config_stock_display')) {
                    $stock = $result['quantity'];
                } else {
                    $stock = $this->language->get('text_instock');
                }

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
                    'stock'       => $stock,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
                    'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
					//'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
				);
			}
//                $log = fopen(DIR_LOGS . 'catalog_controller_product_category__index.log', 'a');
//                fwrite($log,' $data[products] )' . print_r($data['products'], true) . '==' . chr(10) . chr(13));
//                fclose($log);

            // собираем массив с вариантами сортировки
            $urlSort = $this->getUrlParams($params, ['sort','order']);
            
			$data['sorts'] = array();
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
                'href'  => $data['page_url']. '?sort=p.sort_order&order=ASC' . $urlSort
                //'href'  => $this->url->link('search/index', 'sort=p.sort_order&order=ASC' . $urlSort)
				//'href'  => $this->url->link('search/index', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
			);
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
                'href'  => $data['page_url']. '?sort=pd.name&order=ASC' . $urlSort
				//'href'  => $this->url->link('search/index', 'sort=pd.name&order=ASC' . $urlSort)
			);
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
                'href'  => $data['page_url']. '?sort=pd.name&order=DESC' . $urlSort
				//'href'  => $this->url->link('search/index', 'sort=pd.name&order=DESC' . $urlSort)
			);
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
                'href'  => $data['page_url']. '?sort=p.price&order=ASC' . $urlSort
				//'href'  => $this->url->link('search/index', 'sort=p.price&order=ASC' . $urlSort)
			);
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
                'href'  => $data['page_url']. '?sort=p.price&order=DESC' . $urlSort
				//'href'  => $this->url->link('search/index', 'sort=p.price&order=DESC' . $urlSort)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
                    'href'  => $data['page_url']. '?sort=rating&order=DESC' . $urlSort,
					//'href'  => $this->url->link('search/index', 'sort=rating&order=DESC' . $urlSort)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
                    'href'  => $data['page_url']. '?sort=rating&order=ASC' . $urlSort,
					//'href'  => $this->url->link('search/index', 'sort=rating&order=ASC' . $urlSort)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
                'href'  => $data['page_url']. '?sort=p.model&order=ASC' . $urlSort,
				//'href'  => $this->url->link('search/index', 'sort=p.model&order=ASC' . $urlSort)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
                'href'  => $data['page_url']. '?sort=p.model&order=DESC' . $urlSort,
				//'href'  => $this->url->link('search/index', 'sort=p.model&order=DESC' . $urlSort)
			);
            
            
            // собираем массив с вариантами кол-ва элементов на странице
            $urlLimit = $this->getUrlParams($params, ['limit']);
            
			$data['limits'] = array();
			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));
			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
                    'href'  => $data['page_url']. '?limit=' . $value . $urlLimit,
					//'href'  => $this->url->link('search/index', 'limit=' . $value . $urlLimit)
				);
			}
//d($product_total);
//d($page);
//d($limit);
            // паджинатор
            $urlPage = $this->getUrlParams($params, ['page']);
            
			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
            $pagination->url = $data['page_url']. '?page={page}' . $urlPage;
			//$pagination->url = $this->url->link('search/index', 'page={page}' . $urlPage);

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
//			if ($page == 1) {
//			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'canonical');
//			} else {
//				$this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. $page), 'canonical');
//			}
//			
//			if ($page > 1) {
//			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
//			}
//
//			if ($limit && ceil($product_total / $limit) > $page) {
//			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page + 1)), 'next');
//			}
        }

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			$data['continue'] = $this->url->link('common/home');
if(catalog_controller_search_index__getProductsData){
$log = fopen(DIR_LOGS . 'catalog_controller_search_index__getProductsData.log', 'a');
fwrite($log,' =================' . '==' . chr(10) . chr(13));
fwrite($log,' $data )' . print_r($data, true) . '==' . chr(10) . chr(13));
fclose($log);}        

			//$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
            
            // reviews
//            $this->load->model('catalog/review');
//            $reviews = $this->model_catalog_review->getLastReviewsByCategory($category_id, 'r.rating>3');
//            
//            foreach($reviews as &$review) {
//                if (!empty($review['image'])) {
//					$review['image'] = $this->model_tool_image->resize($review['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
//				} else {
//					$review['image'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
//				}
//                
//                $review['href'] = $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $review['product_id']);
//                //$review['date_added'] = date('j M Y', strtotime($review['date_added']));
//                
//                $formatter = new IntlDateFormatter('ru_RU', IntlDateFormatter::FULL, IntlDateFormatter::FULL);
//                $formatter->setPattern('d MMM YYYY');
//                $review['date_added'] = $formatter->format(new DateTime($review['date_added']));
//            }
//			$data['reviews'] = $reviews;
//$log = fopen(DIR_LOGS . 'catalog_controller_product_category__reviews.log', 'a');
//fwrite($log,' $data[reviews] )' . print_r($data['reviews'], true) . '==' . chr(10) . chr(13));
//fclose($log);      
            
//            $data['tecdoc_categories'] = $module = $this->getChild('tecdoc/category', array(
//                'modificationId' => $modificationId,
//            ));

			$this->response->setOutput($this->load->view('product/category', $data));        
                    
//                echo '<pre>';
//        print_r($mods); die;
        
//        foreach ($mods as $modification) {
//			$data['modifications'][] = array(
//                'model' => $modification['model_name'],
//				'modification' => $modification['description'],
//                'year' => $modification['constructioninterval'],
//                'engine' => $modification['engine_name'],
//				'href' => '/mark/'.$typeRequestParam.'/'.$brandRequestParam. '/'. $modelRequestParam.'/'. $this->getLink($modification['description'], $modification['id'])
//			);
//		}
        
        return $data;
    }

    /**
     * формирует элемент ссылки нужного вида
     * формат ссылки [name-in-translit]_[id]
     * в первой части ссылки все символы кроме цифробукв заменяются на дефис, остальной текст транслитерируется
     * @param string $name
     * @param integer $id
     * @return string
     */
    private function getLink($name, $id) {
        // приводим к нижнему регистру
        $str = mb_strtolower($name, 'UTF-8');  
        
        // транслитерируем русские буквы
        $str = $this->translitIso($str); 
        
        // все спец.символы заменяем на дефис
        $str = preg_replace('/[^a-zA-Z0-9\']/', '-', $str); 
        
        // заменяем двойные дефисы на одинарные
        $str = str_replace(
            array("'", '--', '---', '----'), 
            array('', '-', '-', '-'), 
            $str); 
        
        // если первый или последний символ дефис - удаляем его
        $replaced = false;
        $strArr = str_split($str);
        if ($strArr[0] == '-') {
            $strArr[0] = '';
            $replaced = true;
        }
        $lastPos = sizeof($strArr)-1;
        if ($strArr[$lastPos] == '-') {
            $strArr[$lastPos] = '';
            $replaced = true;
        }
        
        if ($replaced) {
            $str = implode('', $strArr);
        }
        
        return $str.'_'. (int)$id;
    }
    
    private function translitIso($str){ 
        // ISO 9-95 
        static $tbl= array(
            'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ж'=>'g', 'з'=>'z',
            'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p',
            'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'ы'=>'y', 'э'=>'e', 'А'=>'A',
            'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ж'=>'G', 'З'=>'Z', 'И'=>'I',
            'Й'=>'Y', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R',
            'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Ы'=>'Y', 'Э'=>'E', 'ё'=>"yo", 'х'=>"h",
            'ц'=>"ts", 'ч'=>"ch", 'ш'=>"sh", 'щ'=>"shch", 'ъ'=>"", 'ь'=>"", 'ю'=>"yu", 'я'=>"ya",
            'Ё'=>"YO", 'Х'=>"H", 'Ц'=>"TS", 'Ч'=>"CH", 'Ш'=>"SH", 'Щ'=>"SHCH", 'Ъ'=>"", 'Ь'=>"",
            'Ю'=>"YU", 'Я'=>"YA", ' '=>"-", '№'=>"N",  'ї'=>"i", 'і'=>"i", 'І'=>"I"
        );//, '«'=>"<", '»'=>">"
        $str = trim($str);
        $str = strtr($str, $tbl);
//        $str = $this->clearTranslit($str);

        return $str;
    }
    
    private function getUrlParams($params, $exlude = array()) {
        $url = '';
        
        foreach($params as $paramName=>$param) {
            if (!in_array($paramName, $exlude) && !empty($param['value']) && $param['value']!=$param['default']) {
                $url .= '&'.$paramName.'='.$param['value'];
            }
        }
        
        return $url;
    }
    
    private function getPageInformation($mode, $pageUrl) {
//        echo '<pre>';
//        var_dump($this->session->data['language']); 
//        var_dump($this->language->all()); 
//        die;
        
        $this->load->model('search/page');
        
        // пытаемся получить инидивидуальную настройку продвигаемой страницы
        if ($mode != 'mark_products') {
            $this->_pageInfo = $this->model_search_page->getPageInfo($pageUrl);
        }
        
        // если индивидуальной настройки нет, получаем ее шаблон
        if (!array_key_exists('meta_title', $this->_pageInfo)) {
            $this->_pageInfo = $this->model_search_page->getPageTemplateInfo($mode);
        }
//        print_r($this->_pageInfo); 
    }
    
    /**
     * устанавливает для страницы мета информацию
     * информация может браться из $this->_pageInfo, если он заполнен
     * если есть ключ is_template, то это шаблон и макро будут заполняться из массива $macroVars
     * если значений нет, то будут использованы значения мета из $default
     * @param array $default значения по умолчанию для мета; keys: meta_title, meta_description
     * @param array $macroVars значения для подстановки в шаблон; keys: manufacturer,model,modification
     */
    private function setPageMetaInformation($default, $macroVars=array()) {
        //if (!empty($this->_pageInfo['is_template'])) {
            $manufacturer = (!empty($macroVars['manufacturer']) ? $macroVars['manufacturer']:'');
            $model = (!empty($macroVars['model']) ? $macroVars['model']:'');
            $modification = (!empty($macroVars['modification']) ? $macroVars['modification']:'');
        //}
        
        // meta-title
        if (empty($this->_pageInfo['meta_title']) && !empty($default['meta_title'])) {
            $this->_pageInfo['meta_title'] = $default['meta_title'];
        }
        if (!empty($this->_pageInfo['meta_title'])) {
            $this->_pageInfo['meta_title'] = str_replace(
                    array('{manufacturer}','{model}','{modification}'), 
                    array($manufacturer,$model,$modification), 
                    $this->_pageInfo['meta_title']);
            
            $this->document->setTitle($this->_pageInfo['meta_title']);
        }
        
        // meta-description
        if (empty($this->_pageInfo['meta_description']) && !empty($default['meta_description'])) {
            $this->_pageInfo['meta_description'] = $default['meta_description'];
        }
        if (!empty($this->_pageInfo['meta_description'])) {
            $this->_pageInfo['meta_description'] = str_replace(
                    array('{manufacturer}','{model}','{modification}'), 
                    array($manufacturer,$model,$modification), 
                    $this->_pageInfo['meta_description']);
            
            $this->document->setDescription($this->_pageInfo['meta_description']);
        }
//        $this->document->setKeywords($category_info['meta_keyword']);
    }
    
    private function gotoError() {
        $this->load->language('error/not_found');
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_error'),
            'href' => 'javascript:void(0)'
        );

        $this->document->setTitle($this->language->get('text_error'));

        $data['heading_title'] = $this->language->get('text_error');

        $data['text_error'] = $this->language->get('text_error');

        $data['continue'] = $this->url->link('common/home');

        $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('error/not_found', $data));
    }
    
    //$model['name'], array($typeRequestParam, $brandRequestParam, $modelRequestParam
//    private function getBreadcrumbs($finalNode, $nodes) {
//        $breadcrumbs = array();
//        
//        $breadcrumbs[] = array(
//			'text' => $this->language->get('text_home'),
//			'href' => $this->url->link('common/home')
//		);
//        
//        $i = 0;
//        $typeRequestParam = '';
//        $brandRequestParam = '';
//        $modelRequestParam = '';
//        foreach($nodes as $node) {
//            switch($i) {
//                case 0: 
//                    $breadcrumbs[] = array(
//                        'text' => $this->language->get('types'),
//                        'href' => '/mark'
//                    );
//                    $typeRequestParam = $node;
//                    break;
//                
//                case 1: 
//                    $breadcrumbs[] = array(
//                        'text' => $this->language->get('brands').' ('.$this->language->get($typeRequestParam).')',
//                        'href' => '/mark/'.$typeRequestParam
//                    );
//                    break;
//                
//                case 2: 
//                    $breadcrumbs[] = array(
//                        'text' => $brand['name'],
//                        'href' => '/mark/'.$typeRequestParam.'/'.$brandRequestParam
//                    );
//                    break;      
//                
//                case 3: 
//                    $breadcrumbs[] = array(
//                        'text' => $model['name'],
//                        'href' => '/mark/'.$typeRequestParam.'/'.$brandRequestParam.'/'.$modelRequestParam
//                    );
//                    break;
//            }
////        $data['breadcrumbs'][] = array(
////            'text' => $this->language->get('brands').' ('.$this->language->get($typeRequestParam).')',
////            'href' => '/mark/'.$typeRequestParam
////        );
////        $data['breadcrumbs'][] = array(
////            'text' => $brand['name'],
////            'href' => '/mark/'.$typeRequestParam.'/'.$brandRequestParam
////        );
//            $i++;
//        }
//        
//        $breadcrumbs[] = array(
//			'text' => $finalNode
//		);
//        
//        return $breadcrumbs;
//    }
    
    public function getBreadcrumbs($mode, $urlData = array(), $textData = array()) {
        $breadcrumbs = array();        
        $breadcrumbs[] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
        
//          $data['breadcrumbs'][] = array(
//            'text' => $this->language->get('types'),
//            'href' => '/mark'
//        );
              
        $breadcrumbs[] = array(
            'text' => $this->language->get('brands'),
            'href' => $this->_langPrefix.'/mark'
            //'text' => $this->language->get('brands').' ('.$this->language->get($typeRequestParam).')',
            //'href' => '/mark/'.$typeRequestParam
        );
        if (in_array($mode, ['models','modifications','products'])) {
            $breadcrumbs[] = array(
                'text' => $textData['brandName'],
                'href' => ($mode == 'models' ? '' : $this->_langPrefix.'/mark/'.$urlData['brandRequestParam']),
                //'href' => '/mark/'.$typeRequestParam.'/'.$brandRequestParam
            );
            
            if (in_array($mode, ['modifications','products'])) {
                $breadcrumbs[] = array(
                    'text' => $textData['modelGroupName'],//$model['name'],
                    'href' => ($mode == 'modifications' ? '' : $this->_langPrefix.'/mark/'.$urlData['brandRequestParam'].'/'.$urlData['modelRequestParam']),
                    //'href' => '/mark/'.$typeRequestParam.'/'.$brandRequestParam.'/'.$modelRequestParam
                );
                
                if (in_array($mode, ['products'])) {
                    $breadcrumbs[] = array(
                        'text' => $textData['modificationName'],
                    );
                }
            }
        }
        
        return $breadcrumbs;
    }
}