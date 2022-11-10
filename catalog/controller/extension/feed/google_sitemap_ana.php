<?php
class ControllerExtensionFeedGoogleSitemap extends Controller {
	
	const MAX_FILESIZE = 7340032;     //7 Mbyte   7340032
  const PRODUCT_PROCESS_NUM = 50000;
  const FILE_EXTENSION = ".xml";

  private $sitemapFile;
  private $baseDir;
  private $productsSQL;
  private $langCode;
	
  public function index()
	{

    $this->productsSQL = 
            " FROM " . DB_PREFIX . "product p" .
             " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)".
           " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) ".
           " WHERE p.status = '1' ".
           "AND p.date_available <= NOW() ".
           "AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . 
           "'  AND (EXISTS (SELECT 1 FROM  " . DB_PREFIX . "product_price pp ".
            "WHERE pp.product_id = p.product_id AND pp.price > 0 ".
            "AND pp.customer_group_id = ". (int)$this->config->get('config_customer_group_id').") )  ".
          "AND p.noindex = '1' ";


		$file = 'sitemap';
		set_time_limit(0);
    ini_set('memory_limit', '-1');
        //parent::__construct();
        //enable seourl-s

		$this->baseDir = dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR;

        // $seoCtrl = new ControllerCommonSeoUrl($this->_registry);
        // $this->url->addRewrite($seoCtrl);
    $this->sitemapFile = $this->baseDir . 'sitemap/' . $file;

    if(!is_dir($this->baseDir . "sitemap"))
    {
      mkdir($this->baseDir . "sitemap");
    }
    
    $files = glob($this->baseDir . "sitemap" . DIRECTORY_SEPARATOR . '*.*');
    if($files)
    {
      foreach($files as $file)
      {
        if(is_file($file))
        {
          unlink($file);
        }
      }
    }

		$output = $this->generate();
    $this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);
	} // end Index()

/****************/
 	private function generate()
 	{
    //HEADER

    $output  = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '.
    'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" '.
    'xsi="http://www.w3.org/2001/XMLSchema-instance" '.
    'xhtml="http://www.w3.org/1999/xhtml" schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
    
		file_put_contents($this->sitemapFile . self::FILE_EXTENSION, $output);

    //INFO PAGES
    $this->writeInfoPages();

    //MANUFACTURERS
    $this->writeManufacturers();

    //CATEGORY
    $this->writeCategory();

      
    // SIMPLE BLOG POST
    $this->writeBlogs();
      
    // PRODUCTS
    $this->writeProducts();

    $this->writeTexDoc();

    if (substr($this->sitemapFile, -5) !='p.xml'){
      $output  = '</urlset>';
      file_put_contents($this->sitemapFile.self::FILE_EXTENSION, $output, FILE_APPEND);
    }
    
      	//CREATE SITEMAP INDEX
    return $this->createIndexFile();
  }  // end Generate

/***** Compress files and create one index file ***********/
    private function createIndexFile()
    {
      clearstatcache();
      $output = '<?xml version="1.0" encoding="UTF-8"?>';
      $output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

      $files = glob($this->baseDir . "sitemap" . DIRECTORY_SEPARATOR . '*');
      if($files)
      {
        foreach($files as $file)
        {
          // $this->gzCompressFile($file);
          $output .= '<sitemap>';
       //   $output .= '<loc>' . HTTP_SERVER . 'sitemap/' . basename($file) . '.gz</loc>';
          $output .= '<loc>' . HTTP_SERVER . 'sitemap/' . basename($file) . '</loc>';
          $output .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
          $output .= '</sitemap>';
        }
      }
      $output .= '</sitemapindex>';
      return $output;
      //file_put_contents($this->baseDir . 'sitemap/'.'sitemapindex.xml', $output);
    } // end createIndexFile()

/****************/
    private function gzCompressFile($source, $level = 9)
    {
      $dest = $source . '.gz';
      $mode = 'wb' . $level;
      $error = false;
      if ($fp_out = gzopen($dest, $mode))
      {
        if ($fp_in = fopen($source,'rb'))
        {
          while (!feof($fp_in))
          {
            gzwrite($fp_out, fread($fp_in, 1024 * 512));
          }
          fclose($fp_in);
        }
        else
        {
          $error = true;
        }
        gzclose($fp_out);
      }
      else
      {
        $error = true;
      }

      if ($error)
      {
        return false;
      }
      else
      {
        return $dest;
      }
    } // end gzCompressFile()

/****************/
    private function writeInfoPages()
    {
      $this->load->model('catalog/information');

      $langs = $this->db->query('Select language_id, code from '. DB_PREFIX . "language");

      foreach ($langs->rows as $lang) {

        $this->Linklang($lang['code']);
        $this->config->set('config_language_id', $lang['language_id']);
        $informations = $this->model_catalog_information->getInformations();

        $output = '';

        foreach ($informations as $information) {
          $output .= '<url>';
          $output .= '<loc>' . $this->url->link('information/information', 'information_id=' . $information['information_id'], true, ['lang' => $this->langCode]) . '</loc>';
        //  $output .= '<changefreq>weekly</changefreq>';
          $output .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
          $output .= '</url>';
        }

        file_put_contents($this->sitemapFile.self::FILE_EXTENSION, $output, FILE_APPEND);
      }
    } // end writeInfoPages()

/****************/
    private function writeManufacturers()
    {
      $this->load->model('catalog/manufacturer');
      $langs = $this->db->query('Select language_id, code from '. DB_PREFIX . "language");

      foreach ($langs->rows as $lang) {

        $this->Linklang($lang['code']);
        $this->config->set('config_language_id', $lang['language_id']);
        $manufacturers = $this->model_catalog_manufacturer->getManufacturers();

        $output = '';

        foreach ($manufacturers as $manufacturer) {
          $output .= '<url>';
          $output .= '<loc>' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id'], true, ['lang' => $this->langCode]) . '</loc>';
         // $output .= '<changefreq>weekly</changefreq>';
          $output .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
          $output .= '</url>';
        }

        file_put_contents($this->sitemapFile.self::FILE_EXTENSION, $output, FILE_APPEND);
      }
    } // end writeManufacturers()

/****************/
    private function writeCategory()
    {
      

      $this->load->model('catalog/category');

      $langs = $this->db->query('Select language_id, code from '. DB_PREFIX . "language");

      foreach ($langs->rows as $lang) {
        
        $output = '';
        
        $this->Linklang($lang['code']);
        $this->config->set('config_language_id', $lang['language_id']);

        $output = $this->getCategories(0);
        file_put_contents($this->sitemapFile.self::FILE_EXTENSION, $output, FILE_APPEND);
      }

      
    }  // end writeCategory()

/****************/
    //Simple Blog 
    protected function writeBlogs()
    {

      $this->load->model('blog/article');

      $data = array();

      $langs = $this->db->query('Select language_id, code from '. DB_PREFIX . "language");

      foreach ($langs->rows as $lang) {
        $output = '';
        $this->Linklang($lang['code']);
        $this->config->set('config_language_id', $lang['language_id']);
        $posts = $this->model_blog_article->getArticles($data);
        if($posts)
        {
          foreach($posts as $post) {
            $output .= '<url>';
            $output .= '<loc>' . $this->url->link('blog/article', 'article_id=' . $post['article_id'], true, ['lang' => $this->langCode]) . '</loc>';
         //   $output .= '<changefreq>weekly</changefreq>';
            $output .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
            $output .= '</url>';
          }
        }
        file_put_contents($this->sitemapFile.self::FILE_EXTENSION, $output, FILE_APPEND);  
      }

    }
    /****************/
    private function writeProducts()
    {
    
      $fileCounter = 0;
      // $this->load->model('catalog/product');
	    $this->load->model('tool/image');

      //  $totalProductNum = $this->model_catalog_product->getTotalProducts();
      
      $langs = $this->db->query('Select language_id, code from '. DB_PREFIX . "language");

      foreach ($langs->rows as $lang) {

          $this->Linklang($lang['code']);
          $totalProductNum = $this->getTotalProducts($lang['language_id']);
          $to = round($totalProductNum / self::PRODUCT_PROCESS_NUM) + 1;
          for($i=0;$i <= $to; $i++)
          {
            $from = $i * self::PRODUCT_PROCESS_NUM;
            $data = array(
              "start" => $from + 1,
              "limit" => self::PRODUCT_PROCESS_NUM
            );
            // $products = $this->model_catalog_product->getProducts($data);
            $products = $this->getProducts($data, $lang['language_id']);
            $output = '';
            foreach ($products as $product)
            {
			        $output .= '<url>';
			        $output .= '<loc>' . $this->url->link('product/product', 'product_id=' . $product['product_id'], true, ['lang' => $this->langCode]) . '</loc>';
			   //     $output .= '<changefreq>weekly</changefreq>';
              $output .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
			         /*if ($product['image'])
			        {
				          $output .= '<image:image>';
				          $output .= '<image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
				          $output .= '<image:caption>' . $product['name'] . '</image:caption>';
				          $output .= '<image:title>' . $product['name'] . '</image:title>';
				          $output .= '</image:image>';
			        }
*/
			        $output .= '</url>';
		        }
            file_put_contents($this->sitemapFile.self::FILE_EXTENSION, $output, FILE_APPEND);
            //clearstatcache();
            if(filesize($this->sitemapFile.self::FILE_EXTENSION)>self::MAX_FILESIZE)
            {
              $output  = '</urlset>';
              file_put_contents($this->sitemapFile.self::FILE_EXTENSION, $output, FILE_APPEND);
              $fileCounter++;
              $this->sitemapFile = rtrim($this->sitemapFile, $fileCounter-1) . $fileCounter;
              $output  = '<?xml version="1.0" encoding="UTF-8"?>';
              $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
              file_put_contents($this->sitemapFile.self::FILE_EXTENSION, $output);
            }
          }

        }
    }  // end writeProducts()

    private function writeTexDoc() {
      // модель tecdoc работает с объектом подключения tecdoc
      $tecdocDb = new DB(DB_DRIVER_TECDOC, DB_HOSTNAME_TECDOC, DB_USERNAME_TECDOC, DB_PASSWORD_TECDOC, DB_DATABASE_TECDOC);
      $this->registry->set('tecdoc', $tecdocDb);

      $this->load->model('search/tecdoc');

      $data = array();
      $_urlPrefix = 'mark/';

      $langs = $this->db->query('Select language_id, code from '. DB_PREFIX . "language");

      foreach ($langs->rows as $lang) {
        $output = '';
        $_langPrefix = '';
        $lang_config = $this->config->get('config_language');

        if ($this->langCode != $lang_config) {
          $_langPrefix = $this->langCode . '/';
        }

        $this->Linklang($lang['code']);
        $this->config->set('config_language_id', $lang['language_id']);

        $brands = $this->model_search_tecdoc->getBrands('');
        foreach($brands as $brand) {
          $output .= '<url>';
          $output .= '<loc>' . HTTP_SERVER . $_langPrefix . $_urlPrefix . $this->getLink($brand['name'], $brand['id']) . '</loc>';
          $output .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
          $output .= '</url>';

          // получаем список моделей
          $models = $this->model_search_tecdoc->getModels('', $brand['id']);
          
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
          }

          foreach ($modelGroups as $modelGroupName=>$modelGroup) {
            $output .= '<url>';
            $output .= '<loc>' . HTTP_SERVER . $_langPrefix . $_urlPrefix . $modelGroup['url'] . '</loc>';
            $output .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
            $output .= '</url>';  
          }
        }
        file_put_contents($this->sitemapFile.self::FILE_EXTENSION, $output, FILE_APPEND);  
      }
    }

    /****************/
    private function getCategories($parent_id, $current_path = '') 
    {
		  $output = '';

		  $results = $this->model_catalog_category->getCategories($parent_id);

		  foreach ($results as $result) {
        if (!$current_path) {
			   $new_path = $result['category_id'];
        } else {
				  $new_path = $current_path . '_' . $result['category_id'];
        }

        $output .= '<url>';
        $output .= '<loc>' . $this->url->link('product/category', 'path=' . $new_path, true, ['lang' => $this->langCode]) . '</loc>';
     //   $output .= '<changefreq>weekly</changefreq>';
        $output .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
        $output .= '</url>';


        $output .= $this->getCategories($result['category_id'], $new_path);
      }

      return $output;
    }  // end getCategories


    private function getTotalProducts($language_id) {

      $sql = "SELECT COUNT(DISTINCT p.product_id) AS total";
      $sql .= $this->productsSQL;
      $sql .= "AND pd.language_id = '" . (int)$language_id ."'";
    
      $query = $this->db->query($sql, false);
      return $query->row['total'];
    }

    private function getProducts($data = array(), $language_id) {

      $sql = "SELECT p.product_id ";
      $sql .= $this->productsSQL;
      $sql .= " AND pd.language_id = '" . (int)$language_id ."'";

      if (isset($data['start']) || isset($data['limit'])) {
        if (!isset($data['start']) || $data['start'] < 0) {
          $data['start'] = 0;
        }

        if ($data['limit'] < 1) {
          $data['limit'] = 20;
        }

        $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
      }

      $product_data = array();
 
      $query = $this->db->query($sql, false);

      foreach ($query->rows as $result) {
        $product_data[$result['product_id']] = $this->getProduct($result['product_id'], $language_id);
      }

      return $product_data;
    }

    public function getProduct($product_id, $language_id) {
      $query = $this->db->query(
        "SELECT  p.product_id, p.image,pd.name AS name FROM " . 
        DB_PREFIX . "product p ".
        "LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) ".
        "WHERE p.product_id = '" . (int)$product_id . "' ".
        "AND pd.language_id = '" . (int)$language_id . "' ", true);

      if ($query->num_rows) {
        return array(
          'product_id'       => $query->row['product_id'],
          'name'            =>  $query->row['name'],
          'image'            => $query->row['image'],
        );
      } else {
        return false;
      }
    }

    public function Linklang($langCode){
      $this->langCode = substr($langCode, 0,2);
      if ($this->langCode == 'ru') {
          $this->langCode = '';
      }
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
}
