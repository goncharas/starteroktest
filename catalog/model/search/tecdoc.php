<?php
class ModelSearchTecdoc extends Model {
    /**
     * Type auto
     * @var [passenger|commercial|motorbike|engine|axle|transporter]
     */
    private $type;
    private $allowedTypes = array('passenger','commercial','motorbike');
    
    public function __construct($registry)
    {
        // конструкто вызывается при каждом обращении к методу!!! таким образом невозможно один раз задать для модели режим работы в $type
//        echo '<br>create!!!!!!!<br>';
        parent::__construct($registry);
        
        $this->type = 'passenger';
    }
    
    /**
     * type setter
     * @param string $type passenger|commercial|motorbike|engine|axle
     */
    public function setType($type) {
        if (in_array($type, $this->allowedTypes)) {
            $this->type = $type;
        }
    }
    
    public function getTypes() {
        return $this->allowedTypes;
    }
    
    /**
     * Марки авто (производители)
     * @return array
     */
	public function getBrands($type) {
        $where = '';
        //echo $this->type; die;
        $this->type = $type;
        
        switch ($this->type) {
            case 'passenger':
                $where = " AND ispassengercar = 'True'";
                break;
            case 'commercial':
                $where = " AND iscommercialvehicle = 'True'";
                break;
            case 'motorbike':
                $where = " AND ismotorbike  = 'True' AND haslink = 'True'";
                break;
            case 'engine':
                $where = " AND isengine = 'True'";
                break;
            case 'axle':
                $where = " AND isaxle = 'True'";
                break;
            case 'transporter':
                $where = " AND istransporter = 'True'";
                break;
        }
        
        if (empty($where)) {
            $where = " AND (ismotorbike='True' OR ispassengercar='True' OR iscommercialvehicle='True')";
        }

        $order = $this->type == 'motorbike' ? 'description' : 'matchcode';      
      
        $query = $this->tecdoc->query("
            SELECT id, description name
            FROM manufacturers
            WHERE canbedisplayed = 'True' " . $where . "
            ORDER BY " . $order, false
        );       
        
        return $query->rows;
	}
    
    /**
     * Получить список моделей по производителю
     * @param int $brand_id идентификатор производителя
     * @param string $pattern дополнительный поиск по названию модели
     * @return array
     */
    public function getModels($type, $brand_id, $pattern = null)
    {
        $this->type = $type;
        $where = '';
        
        switch ($this->type) {
            case 'passenger':
                $where = " AND ispassengercar = 'True'";
                break;
            case 'commercial':
                $where = " AND iscommercialvehicle = 'True'";
                break;
            case 'motorbike':
                $where = " AND ismotorbike  = 'True'";
                break;
            case 'engine':
                $where = " AND isengine = 'True'";
                break;
            case 'axle':
                $where = " AND isaxle = 'True'";
                break;
            case 'transporter':
                $where = " AND istransporter = 'True'";
                break;
        }

        if (empty($where)) {
            $where = " AND (ismotorbike='True' OR ispassengercar='True' OR iscommercialvehicle='True')";
        }
        if ($pattern != null) {
            $where .= " AND description LIKE '" . $pattern . "%'";
        }

        $query = $this->tecdoc->query("
            SELECT id, description AS name, constructioninterval, model_group, model_group_url
            FROM models
            WHERE canbedisplayed = 'True'
            AND manufacturerid = " . (int)$brand_id . " " . $where . "
            ORDER BY model_group, description
        ", false);
        return $query->rows;
    }

    public function getModifications($type, $whereOptions)
    {
        $this->type = $type;
        
        $where = '';
        if (!empty($whereOptions['modelId'])) {
            $where = " AND (A.modelid = " . (int)$whereOptions['modelId'].")";
        }
        if (!empty($whereOptions['models'])) {
            $where = " AND (A.modelid IN (" . implode(',', $whereOptions['models']) ."))";
        }
        
        switch ($this->type) {
            case 'passenger':
//                $query = $this->tecdoc->query("
//					SELECT id, fulldescription name, a.attributegroup, a.attributetype, a.displaytitle, a.displayvalue
//					FROM passanger_cars pc 
//					LEFT JOIN passanger_car_attributes a on pc.id = a.passangercarid
//					WHERE canbedisplayed = 'True'
//					AND modelid = " . (int)$model_id . " AND ispassengercar = 'True'");
                $sql = "
                    SELECT A.id, A.description, A.constructioninterval, M.description as model_name, E.description as engine_name 
                    FROM passanger_cars A 
                    left join models M on A.modelid = M.id AND M.canbedisplayed = 'True'
                    left join passanger_car_engines PCE on A.id = PCE.id
                    left join engines E on E.id = PCE.engineid AND E.canbedisplayed = 'True'
                    WHERE A.ispassengercar='True'  AND A.canbedisplayed = 'True' ". $where ."
                    ORDER BY model_name, A.description"; // -- AND A.canbedisplayed = 'True'
                break;
            case 'commercial':
//                $query = $this->tecdoc->query("
//					SELECT id, fulldescription name, a.attributegroup, a.attributetype, a.displaytitle, a.displayvalue
//					FROM commercial_vehicles cv 
//					LEFT JOIN commercial_vehicle_attributes a on cv.id = a.commercialvehicleid
//					WHERE canbedisplayed = 'True'
//					AND modelid = " . (int)$model_id . " AND iscommercialvehicle = 'True'");
//                break;
                $sql = "
                    SELECT A.id, A.description, A.constructioninterval, M.description as model_name, E.description as engine_name 
                    FROM commercial_vehicles A 
                    left join models M on A.modelid = M.id AND M.canbedisplayed = 'True'
                    left join commercial_vehicle_engines PCE on A.id = PCE.id
                    left join engines E on E.id = PCE.engineid AND E.canbedisplayed = 'True'
                    WHERE A.iscommercialvehicle='True'  AND A.canbedisplayed = 'True' ". $where ."
                    ORDER BY model_name, A.description"; // -- AND A.canbedisplayed = 'True'
                break;
            case 'motorbike':
//                $query = $this->tecdoc->query("
//					SELECT id, fulldescription name, a.attributegroup, a.attributetype, a.displaytitle, a.displayvalue
//					FROM motorbikes m 
//					LEFT JOIN motorbike_attributes a on m.id = a.motorbikeid
//					WHERE canbedisplayed = 'True'
//					AND modelid = " . (int)$model_id . " AND ismotorbike = 'True'");
//                break;
                $sql = "
                    SELECT A.id, A.description, A.constructioninterval, M.description as model_name, '' as engine_name 
                    FROM motorbikes A 
                    left join models M on A.modelid = M.id AND M.canbedisplayed = 'True'
                    WHERE A.ismotorbike='True'  AND A.canbedisplayed = 'True' ". $where ."
                    ORDER BY model_name, A.description"; // -- AND A.canbedisplayed = 'True'
                //E.description left join motorbike_engines PCE on A.id = PCE.id left join engines E on E.id = PCE.engineid AND E.canbedisplayed = 'True'
                break;
//            case 'transporter':
//                $query = $this->tecdoc->query("
//                    SELECT A.description, A.constructioninterval, M.description as model_name, E.description as engine_name 
//                    FROM passanger_cars A 
//                    left join models M on A.modelid = M.id AND M.canbedisplayed = 'True'
//                    left join passanger_car_engines PCE on A.id = PCE.id
//                    left join engines E on E.id = PCE.engineid AND E.canbedisplayed = 'True'
//                    WHERE A.modelid = " . (int)$model_id . " AND A.ispassengercar='True' 
//                    ORDER BY A.description"); // -- AND A.canbedisplayed = 'True'
//                break;
//            case 'engine':
//                $sql = "
//					SELECT id, fulldescription name, salesDescription, a.attributegroup, a.attributetype, a.displaytitle, a.displayvalue
//					FROM engines e 
//					LEFT JOIN engine_attributes a on e.id= a.engineid
//					WHERE canbedisplayed = 'True'
//					AND manufacturerId = " . (int)$model_id . " AND isengine = 'True'";
//                break;
//            case 'axle':
////                $query = $this->tecdoc->query("
////					SELECT id, fulldescription name, a.attributegroup, a.attributetype, a.displaytitle, a.displayvalue
////					FROM axles ax 
////					LEFT JOIN axle_attributes a on ax.id= a.axleid
////					WHERE canbedisplayed = 'True'
////					AND modelid = " . (int)$model_id . " AND isaxle = 'True'");
//                $sql = "
//                    SELECT A.description, A.constructioninterval, M.description as model_name, '' as engine_name 
//                    FROM axles A 
//                    left join models M on A.modelid = M.id AND M.canbedisplayed = 'True'
//                    WHERE A.modelid = " . (int)$model_id . " AND A.isaxle='True'  AND A.canbedisplayed = 'True'
//                    ORDER BY A.description"; // -- AND A.canbedisplayed = 'True'
//                break;
        }
        
        if (!empty($sql)) {
            $query = $this->tecdoc->query($sql, false);
        }
        
        return isset($query) ? $query->rows : array();
    }
    
    public function getProducts($type, $modificationId, $tecdocCategoryId, $brandId) {
        if (!in_array($type, $this->allowedTypes)) {
            return array();
        }
        
        switch ($type) {
            case 'passenger':
                $tableName_pds = 'passanger_car_pds';
                $tableName_prd = 'passanger_car_prd';
                $pdsFieldKey = 'passangercarid';
                $linkagetype = ' AND al.linkagetypeid IN (2,7,14,19) ';
                break;
            case 'commercial':
                $tableName_pds = 'commercial_vehicle_pds';
                $tableName_prd = 'commercial_vehicle_prd';
                $pdsFieldKey = 'commertialvehicleid';
                $linkagetype = ' AND al.linkagetypeid IN (7,14,16,19) ';
                break;
            case 'motorbike':
                $tableName_pds = 'motorbike_pds';
                $tableName_prd = 'motorbike_prd';
                $pdsFieldKey = 'motorbikeid';
                $linkagetype = ' AND al.linkagetypeid = 2 ';
                break;
        }
        
        $sessionId = $this->session->getId();
        
        // удаляем все для данной сессии или для чужих, если данные устарели
        $query = $this->tecdoc->query("DELETE FROM session_search WHERE session_id = '".$sessionId."' OR date_inserted < DATE_SUB(NOW(), INTERVAL 1 HOUR)", false);
        
        $query = $this->tecdoc->query("
            INSERT INTO session_search (part_number, supplierid, supplier_name, product_name, session_id, date_inserted)
            SELECT 
                REPLACE(REPLACE(REPLACE(REPLACE(al.datasupplierarticlenumber, '/',''), '.',''), '-',''), ' ','') as part_number, 
                s.id, IF(s.description_changed <> '', s.description_changed, s.description) as supplier_name, 
                 prd_origin.description as product_name, 
                '".$sessionId."', NOW()
            FROM article_links al 
            LEFT JOIN suppliers s on s.id = al.supplierid
            INNER JOIN prd prd_origin on (prd_origin.id = al.productid) AND (prd_origin.status = 1)
            WHERE al.linkageid =  ". (int)$modificationId ."
                ". $linkagetype ."
                AND al.productid = ". (int)$tecdocCategoryId ."
            ORDER BY s.description, al.datasupplierarticlenumber
        ", false);
//        and prd.enabled  = 1
//            AND pds.nodeid =  100259  --  масляный фильтр passanger_car_trees.id     
        
        //prd.description as product_name, 
        //   JOIN ". $tableName_prd ." prd on prd.id = prd_origin.id  
        //  JOIN ". $tableName_pds ." pds on al.supplierid = pds.supplierid
        //al.productid = pds.productid
        //   AND al.linkageid = pds.". $pdsFieldKey. "
        //  AND prd_origin.id = ". (int)$tecdocCategoryId ."
        
        // дополняем сессийную промежуточную выборку
        $query = $this->tecdoc->query("
            INSERT INTO session_search (part_number, supplier_name, session_id, date_inserted)
            SELECT distinct 
                REPLACE(REPLACE(REPLACE(REPLACE(article_oe.OENbr, '/',''), '.',''), '-',''), ' ',''), 
                manufacturers.description, 
                '".$sessionId."', 
                NOW() 
            FROM article_oe
            left join suppliers on suppliers.id = article_oe.supplierid
            left join session_search on article_oe.datasupplierarticlenumber = session_search.part_number
            left join manufacturers on manufacturers.id = article_oe.manufacturerId
            WHERE article_oe.SupplierId = session_search.supplierid
                and article_oe.manufacturerId = ". (int)$brandId ."   
                and session_search.session_id = '".$sessionId."'
        ", false) ;
        
        return true;
        // технический запрос, нужный для отладки
        //$query = $this->tecdoc->query("SELECT * FROM session_search WHERE session_id = '".$sessionId."'", true);
        
        //return $query->rows; // это технический возврат, нужный только для отладки, выключить потом
    }
    
    /**
     * возвращает список моделей по группе
     * в том случае, если вдруг не пустой ID, будет возвращена одна модель
     * @param string $group
     * @param int $id
     * @return array
     */
    public function getModelsByGroup($group, $id=0) {
        $query = $this->tecdoc->query("
            SELECT id, description AS name, model_group, model_group_url, 
                iscommercialvehicle, ismotorbike, ispassengercar
            FROM models
            WHERE canbedisplayed = 'True' 
            ". (!empty($id) ? " AND id = ". (int)$id : " AND model_group = '". $this->tecdoc->escape($group) ."'")
        , false);
        
        return $query->rows;
	}
    
    public function getBrandById($id) {
        $query = $this->tecdoc->query("
            SELECT id, description name
            FROM manufacturers
            WHERE canbedisplayed = 'True' AND id = ". (int)$id
        , false);
        
        return $query->row;
	}
    
    public function getModelById($id) {
        $query = $this->tecdoc->query("
            SELECT id, description name
            FROM models
            WHERE canbedisplayed = 'True' AND id = ". (int)$id
        , false);
        
        return $query->row;
	}
    
    public function getModificationById($id, $type) {
        if (!in_array($type, $this->allowedTypes)) {
            return array();
        }
        
        switch ($type) {
            case 'passenger':
                $tableName = 'passanger_cars';
                break;
            case 'commercial':
                $tableName = 'commercial_vehicles';
                break;
            case 'motorbike':
                $tableName = 'motorbikes';
                break;
        }
        
        $query = $this->tecdoc->query("
            SELECT id, description name
            FROM ".$tableName."
            WHERE canbedisplayed = 'True' AND id = ". (int)$id
        , false);
        
        return $query->row;
	}
}