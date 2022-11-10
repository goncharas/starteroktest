<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerStartupSeoUrl extends Controller {
	
	//seopro start
		private $seo_pro;
		public function __construct($registry) {
			parent::__construct($registry);	
			$this->seo_pro = new SeoPro($registry);
		}
	//seopro end
        
    /* alex */
    private $allowedLangs = [
        'ru' => 1,
        'en' => 2,
        'uk' => 3,
    ];
    private $allowedCities = [
        'kiev' => 1,
        'odessa' => 0,
        'london' => 2,
    ];
    /* alex */
	
	public function index() {

		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}
		
		// Decode URL
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);
	        
            if ((!empty($parts[0]) && $parts[0] == 'mark') || (!empty($parts[1]) && $parts[1] == 'mark')) {
                //$skip = true;
                $this->request->get['route'] = 'search/index';
                
                $startIndex = 0;
                $lang = 'ru';
                
                if (!empty($parts[1]) && $parts[1] == 'mark') {
                    $startIndex = 1;
                    
                    if (!empty($parts[0])) {
                        $lang = $parts[0];
                        $this->request->get['lang'] = $lang;
                    }
                }
                        
                if (!empty($this->allowedLangs[$lang])) {
                    $this->registry->set('urlLang', ($lang!='ru'? $lang : ''));
                    $this->setLanguageById($this->allowedLangs[$lang]);
                }

//                if (!empty($parts[1])) {
//                    $this->request->get['type'] = $parts[1];
//                }
                if (!empty($parts[$startIndex+1])) {
                    $this->request->get['brand'] = $parts[$startIndex+1];
                }
                if (!empty($parts[$startIndex+2])) {
                    $this->request->get['model'] = $parts[$startIndex+2];
                }
                if (!empty($parts[$startIndex+3])) {
                    $this->request->get['modification'] = $parts[$startIndex+3];
                }
                if (!empty($parts[$startIndex+4])) {
                    $this->request->get['category'] = $parts[$startIndex+4];
                }

            } else {     
      
                // alex - подпорка для языковых префиксов на главной
                if (array_key_exists($this->request->get['_route_'], $this->allowedLangs)) {
                    $this->request->get['_route_'] = 'common/home';
                    $this->request->get['route'] = 'common/home';
                }
                
                /* alex - это подпорка для языковых префиксов с техническими урлами */
                if (!empty($parts[1]) && $parts[1]=='index.php' ) { // && !empty($this->request->get['lang'])
                    $lang = array_shift($parts);
                    
                    if (!empty($this->allowedLangs[$lang])) {
                        $this->registry->set('urlLang', ($lang!='ru'? $lang : ''));
                        $this->setLanguageById($this->allowedLangs[$lang]);
                    }

                    return;
                }
                $this->savePrefixesForProcessing($parts);
                /* alex end */
			
                //seopro prepare route
                if($this->config->get('config_seo_pro')){		
                    $parts = $this->seo_pro->prepareRoute($parts);
                }
                //seopro prepare route end

                // remove any empty arrays from trailing
                if (utf8_strlen(end($parts)) == 0) {
                    array_pop($parts);
                }

                foreach ($parts as $part) {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

                    if ($query->num_rows) {
                        $url = explode('=', $query->row['query']);

                        if ($url[0] == 'product_id') {
                            $this->request->get['product_id'] = $url[1];
                        }

                        if ($url[0] == 'category_id') {
                            if (!isset($this->request->get['path'])) {
                                $this->request->get['path'] = $url[1];
                            } else {
                                $this->request->get['path'] .= '_' . $url[1];
                            }
                        }

                        if ($url[0] == 'manufacturer_id') {
                            $this->request->get['manufacturer_id'] = $url[1];
                        }

                        if ($url[0] == 'information_id') {
                            $this->request->get['information_id'] = $url[1];
                        }

                        if ($query->row['query'] && $url[0] != 'information_id' && $url[0] != 'manufacturer_id' && $url[0] != 'category_id' && $url[0] != 'product_id') {
                            $this->request->get['route'] = $query->row['query'];
                        }
                    } else {
                        if(!$this->config->get('config_seo_pro')){		
                            $this->request->get['route'] = 'error/not_found';
                        }

                        break;
                    }
                }

                if (!isset($this->request->get['route'])) {
                    if (isset($this->request->get['product_id'])) {
                        $this->request->get['route'] = 'product/product';
                    } elseif (isset($this->request->get['path'])) {
                        $this->request->get['route'] = 'product/category';
                    } elseif (isset($this->request->get['manufacturer_id'])) {
                        $this->request->get['route'] = 'product/manufacturer/info';
                    } elseif (isset($this->request->get['information_id'])) {
                        $this->request->get['route'] = 'information/information';
                    }
                }
            }
            
        } else {
            /* alex */
            $lang = 'ru';
            $this->registry->set('urlLang', '');
            $this->setLanguageById($this->allowedLangs[$lang]);
            /* alex end */
		}

		//seopro validate
		if($this->config->get('config_seo_pro')){		
			$this->seo_pro->validate();
		}
        //seopro validate

	}

	public function rewrite($link, $prefixes = null) {
        //$catalog_controler_startup_seo_url__rewrite = false;
                                    if(catalog_controler_startup_seo_url__rewrite){
                                    $log = fopen(DIR_LOGS . 'catalog_controler_startup_seo_url__rewrite.log', 'w');
                                    fwrite($log,'**********************************' . chr(13) . chr(13));
                                    $t = microtime(true);
                                    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
                                    $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
                                    fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
                                    fwrite($log,'$link ) ' . print_r($link,true) . chr(13) . chr(13));
                                    fclose($log);}

		$url_info = parse_url(str_replace('&amp;', '&', $link));

		if($this->config->get('config_seo_pro')){		
            $url = null;
        } else {
            $url = '';
		}

		$data = array();

		parse_str($url_info['query'], $data);

        $ctlg_prefix = (!empty($data['route']) && $data['route'] == 'product/category') ? '/category' : '';
                                    if(catalog_controler_startup_seo_url__rewrite){      
                                    $log = fopen(DIR_LOGS . 'catalog_controler_startup_seo_url__rewrite.log', 'a');
                                    fwrite($log,'++++++++++++++++++++++++++++++++++ if ($condition)' . chr(13) . chr(13));
                                    if(!empty($data['route']))
                                    fwrite($log,'$ctlg_prefix = (!empty($data[route]) && $data[route] == product/category) ? "/category" : "";  $data[route] ) ' . print_r($data['route'],true) . chr(13) . chr(13));
                                    fwrite($log,'$ctlg_prefix = (!empty($data[route]) && $data[route] == product/category) ? "/category" : "";  $ctlg_prefix ) ' . print_r($ctlg_prefix,true) . chr(13) . chr(13));
                                    fclose($log);}
        
        // alex
        $urlData = array();
        parse_str($url_info['query'], $urlData);
        // alex end
                 
                                    if(catalog_controler_startup_seo_url__rewrite){      
                                    $log = fopen(DIR_LOGS . 'catalog_controler_startup_seo_url__rewrite.log', 'a');
                                    fwrite($log,'++++++++++++++++++++++++++++++++++' . chr(13) . chr(13));
                                    fwrite($log,'$url_info = parse_url(str_replace(&amp;, &, $link)); ) ' . print_r($url_info,true) . chr(13) . chr(13));
                                    fwrite($log,'parse_str($url_info[query], $data); ) ' . print_r($data,true) . chr(13) . chr(13));
                                    fclose($log);}
		
		//seo_pro baseRewrite
		if($this->config->get('config_seo_pro')){		
			list($url, $data, $postfix) =  $this->seo_pro->baseRewrite($data, (int)$this->config->get('config_language_id'));	
                                    if(catalog_controler_startup_seo_url__rewrite){      
                                    $log = fopen(DIR_LOGS . 'catalog_controler_startup_seo_url__rewrite.log', 'a');
                                    fwrite($log,'++++++++++++++++++++++++++++++++++ if($this->config->get(config_seo_pro))' . chr(13) . chr(13));
                                    fwrite($log,'list($url, $data, $postfix) =  $this->seo_pro->baseRewrite($data, (int)$this->config->get(config_language_id));	(int)$this->config->get(config_language_id) ) ' . print_r((int)$this->config->get('config_language_id'),true) . chr(13) . chr(13));
                                    fwrite($log,'$url ) ' . print_r($url,true) . chr(13) . chr(13));
                                    fwrite($log,'$data ) ' . print_r($data,true) . chr(13) . chr(13));
                                    fwrite($log,'$postfix ) ' . print_r($postfix,true) . chr(13) . chr(13));
                                    fclose($log); }
		}
		//seo_pro baseRewrite
        
        // alex
        if ($urlData['route'] == 'search/index') {
            $_eteRoute = $this->request->get['_route_'];
            $_eteRouteParts = explode('/', $_eteRoute);

            if (!empty($this->request->get['lang']) && $_eteRouteParts[0] == $this->request->get['lang']) {
                // убираем из частей урла ненужный нам языковой префикс
                array_shift($_eteRouteParts);
            }
            
            $link = $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : ''). '/'.implode('/', $_eteRouteParts);
            $u = $this->rewriteLinkWithPrefixes($link, $url_info['scheme'].'://'.$url_info['host'], $urlData, $prefixes);
            
            return $u;
        }
//        if (!is_null($prefixes)) {
//            d($link);
//            d($url_info);
//            d($urlData);
//        }        
        // alex end

		foreach ($data as $key => $value) {
                                    if(catalog_controler_startup_seo_url__rewrite){      
                                    $log = fopen(DIR_LOGS . 'catalog_controler_startup_seo_url__rewrite.log', 'a');
                                    fwrite($log,'++++++++++++++++++++++++++++++++++ foreach ($data as $key => $value)' . chr(13) . chr(13));
                                    fwrite($log,'$key ) ' . print_r($key,true) . chr(13) . chr(13));
                                    fwrite($log,'$value ) ' . print_r($value,true) . chr(13) . chr(13));
                                    fclose($log);}
			if (isset($data['route'])) {
                $ctlg_prefix = (!empty($data['route']) && $data['route'] == 'product/category') ? '/category' : '';
                
				if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
                    $sql = "SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'";
					$query = $this->db->query($sql);
                                    if(catalog_controler_startup_seo_url__rewrite){      
                                    $log = fopen(DIR_LOGS . 'catalog_controler_startup_seo_url__rewrite.log', 'a');
                                    fwrite($log,'++++++++++++++++++++++++++++++++++ foreach ($data as $key => $value)' . chr(13) . chr(13));
                                    fwrite($log,'if (($data[route] == product/product && $key == product_id) || (($data[route] == product/manufacturer/info || $data[route] == product/product) && $key == manufacturer_id) || ($data[route] == information/information && $key == information_id)) ' . chr(13) . chr(13));
                                    fwrite($log,'$sql ) ' . print_r($sql,true) . chr(13) . chr(13));
                                    fwrite($log,'$query->num_rows ) ' . print_r($query->num_rows,true) . chr(13) . chr(13));
                                    fclose($log);}

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
                    
//                } elseif ($data['route'] == 'search/index') {
//                    $_eteRoute = $this->request->get['_route_'];
//                    $_eteRouteParts = explode('/', $_eteRoute);
//
//                    if (!empty($this->request->get['lang']) && $_eteRouteParts[0] == $this->request->get['lang']) {
//                        // убираем из частей урла ненужный нам языковой префикс
//                        array_shift($_eteRouteParts);
//                    }
//                    $url = '/'.implode('/', $_eteRouteParts);
                    
				} elseif ($key == 'path') {
					$categories = explode('_', $value);
                                    if(catalog_controler_startup_seo_url__rewrite){      
                                    $log = fopen(DIR_LOGS . 'catalog_controler_startup_seo_url__rewrite.log', 'a');
                                    fwrite($log,'++++++++++++++++++++++++++++++++++ foreach ($data as $key => $value)' . chr(13) . chr(13));
                                    fwrite($log,'elseif ($key == path) $categories = explode("_", $value);  ) ' . print_r($categories,true) . chr(13) . chr(13));
                                    fwrite($log,'$value ) ' . print_r($value,true) . chr(13) . chr(13));
                                    fclose($log);}

					foreach ($categories as $category) {
                        $sql = "SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'category_id=" . (int)$category . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'";
						$query = $this->db->query($sql);

                                    if(catalog_controler_startup_seo_url__rewrite){      
                                    $log = fopen(DIR_LOGS . 'catalog_controler_startup_seo_url__rewrite.log', 'a');
                                    fwrite($log,'++++++++++++++++++++++++++++++++++ foreach ($data as $key => $value)  foreach ($categories as $category)' . chr(13) . chr(13));
                                    fwrite($log,'$sql  ) ' . print_r($sql,true) . chr(13) . chr(13));
                                    fwrite($log,'$query->num_rows ) ' . print_r($query->num_rows,true) . chr(13) . chr(13));
                                    fclose($log);}
						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				}
			}
		}

		//seo_pro add blank url
		if($this->config->get('config_seo_pro')) {		
			$condition = ($url !== null);
		} else {
			$condition = $url;
		}
        
        // убираем технический урл для главной
        if (!empty($urlData['route']) && $urlData['route'] == 'common/home') {
            $link = str_replace('/index.php?route=common/home','',$link);
        }

		if ($condition) {
                                    if(catalog_controler_startup_seo_url__rewrite){      
                                    $log = fopen(DIR_LOGS . 'catalog_controler_startup_seo_url__rewrite.log', 'a');
                                    fwrite($log,'++++++++++++++++++++++++++++++++++ if ($condition)' . chr(13) . chr(13));
                                    fwrite($log,'$condition  ) ' . print_r($condition,true) . chr(13) . chr(13));
                                    fclose($log);}
			if($this->config->get('config_seo_pro')){		
				if($this->config->get('config_page_postfix') && $postfix) {
					$url .= $this->config->get('config_page_postfix');
				} elseif($this->config->get('config_seopro_addslash')) {
					$url .= '/';
				}
         
                if( strrpos($url,'/') ){
				   $url = substr($url,strrpos($url,'/'));
                }
                                    if(catalog_controler_startup_seo_url__rewrite){      
                                    $log = fopen(DIR_LOGS . 'catalog_controler_startup_seo_url__rewrite.log', 'a');
                                    fwrite($log,'++++++++++++++++++++++++++++++++++ if ($condition)' . chr(13) . chr(13));
                                    fwrite($log,'$url ) ' . print_r($url,true) . chr(13) . chr(13));
                                    fclose($log);}
                $url = $ctlg_prefix . $url;
			}
			
		//seo_pro add blank url
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

            /* alex */
			$u = $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
            //return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
            /* alex */
            $u = $link;
			//return $link;
		}

        /* alex */
        $u = $this->rewriteLinkWithPrefixes($u, $url_info['scheme'].'://'.$url_info['host'], $urlData, $prefixes);
        
        return $u;
	}
    
    /* alex - поддержка языкового и регионального префикса */
    protected function rewriteLinkWithPrefixes($url, $schemeAndHost, $urlData, $manualPrefixes = null) {
//        var_dump($schemeAndHost);
        //$catalog_controler_startup_seo_url__rewriteLinkWithPrefixes = false;      
                                if(catalog_controler_startup_seo_url__rewriteLinkWithPrefixes){      
                                $log = fopen(DIR_LOGS . 'catalog_controler_startup_seo_url__rewriteLinkWithPrefixes.log', 'w');
                                fwrite($log,'**********************************' . chr(13) . chr(13));
                                $t = microtime(true);
                                $micro = sprintf("%06d",($t - floor($t)) * 1000000);
                                $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
                                fwrite($log, $d->format("Y-m-d H:i:s.u") . '==' . chr(10) . chr(13));
                                fwrite($log,'$url ) ' . print_r($url,true) . chr(13) . chr(13));
                                fwrite($log,'$schemeAndHost ) ' . print_r($schemeAndHost,true) . chr(13) . chr(13));
                                fwrite($log,'$urlData ) ' . print_r($urlData,true) . chr(13) . chr(13));
                                if(isset($manualPrefixes))
                                fwrite($log,'$manualPrefixes ) ' . print_r($manualPrefixes,true) . chr(13) . chr(13));
                                fclose($log);}
        
        $prefix = '';
        
        if ($this->isLinkNeedPrefixes($urlData, 'lang')) {
            if (isset($manualPrefixes['lang'])) {
                $prefix = $manualPrefixes['lang'];
            } elseif ($this->registry->has('urlLang')) {
                $prefix = $this->registry->get('urlLang');
            }
        }
        if ($this->isLinkNeedPrefixes($urlData, 'region')) {
            if (isset($manualPrefixes['region'])) {
                $prefix .= (!empty($prefix) ? '/' : '') . $manualPrefixes['region'];
            } elseif ($this->registry->has('urlRegion') && !empty($this->registry->get('urlRegion') )) {
                $prefix .= (!empty($prefix) ? '/' : '') . $this->registry->get('urlRegion');
            }
        }
        if (!empty($prefix)) {
//            if (strpos($url,'http://') !== false) {
//                $url = str_replace('http://', 'http://'.$prefix.'/', $url);
//            } elseif (strpos($url,'https://') !== false) {
//                $url = str_replace('https://', 'https://'.$prefix.'/', $url);
//            } else {
//                $url = $prefix. '/'. $url;
//            }
            $url = str_replace($schemeAndHost, $schemeAndHost.'/'.$prefix, $url);
        }
        
        return $url;
    }
    
    protected function isLinkNeedPrefixes($data, $prefixType) {
        $routesLang = [
            'common/home',
            'information/contact','information/information','information/sitemap',
            'product/category','product/product','product/manufacturer','product/special',
            'blog/latest','blog/category',
        ];
        $routesRegions = [
            'common/home',
            'information/contact','information/information','information/sitemap',
            'product/category','product/manufacturer','product/special', //'product/product',
            'blog/latest','blog/category',
        ];
                
        if ($prefixType == 'lang') {
            if ($data['route'] != 'common/currency/currency') {
                return true;
            }
//            if (in_array($data['route'], $routesLang)) {
//                return true;
//            }
        }
        if ($prefixType == 'region') {
            if (in_array($data['route'], $routesRegions)) {
                return true;
            }
        }
        
        return false;
    }
    
    protected function setLanguageById($langId) {
        $code = '';
		
//		$this->load->model('localisation/language');
		
		$languages = $this->model_localisation_language->getLanguages();
        
        foreach($languages as $language) {
            if ($language['language_id'] == $langId) {
                $code = $language['code'];
                break;
            }
        }
        
		if (!empty($code)) {
			$this->session->data['language'] = $code;
            setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
            
            // Overwrite the default language object
            $language = new Language($code);
            $language->load($code);

            $this->registry->set('language', $language);

            // Set the config language_id
            $this->config->set('config_language_id', $languages[$code]['language_id']);	
            $this->config->set('config_language', $code);
		}	
    }
    
    protected function savePrefixesForProcessing(&$parts) {
        if (strpos($this->request->get['_route_'], 'admin/') === false) {

            if (isset($parts[0]) && array_key_exists($parts[0], $this->allowedLangs)) {
                $lang = array_shift($parts);
            } else {
                $lang = 'ru';
            }
//             print_r('<Br>lang: '.$lang);

            if (!empty($this->allowedLangs[$lang])) {
                $this->registry->set('urlLang', ($lang!='ru'? $lang : ''));
                $this->setLanguageById($this->allowedLangs[$lang]);
            }

            $defaultRegion = true;
            
            if (isset($parts[0]) && array_key_exists($parts[0], $this->allowedCities)) {
                $region = array_shift($parts);

                if (!empty($this->allowedCities[$region])) {
                    $defaultRegion = false;
                    //$this->request->get['store_id'] =  $this->allowedCities[$region];
                    $this->registry->set('urlRegion', $region);
                    $this->config->set('config_store_id', $this->allowedCities[$region]);
                    $this->session->data['region'] = $region;
                }
                print_r('<Br>region: '.$region);
            }
            
            if ($defaultRegion) {
                $this->registry->set('urlRegion', '');
                $this->config->set('config_store_id', 0);
                $this->session->data['region'] = '';
            }
        }
//         print_r('<Br>config_language: '.$this->config->get('config_language'));
//         print_r('<br>session lang: '. $this->session->data['language']);
        // print_r('<br>session region: '. (!empty($this->session->data['region'])?$this->session->data['region']:''));
//            var_dump($parts); 
//            die;         
    }
    /* alex end */
}
