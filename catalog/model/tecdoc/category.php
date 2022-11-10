<?php
class ModelTecdocCategory extends Model {
    private $allowedTypes = array('passenger','commercial','motorbike');
    
    public function getCategoriesByModification($modificationId, $type = 'passenger') {
        if (!in_array($type, $this->allowedTypes)) {
            return array();
        }
        
//        echo "call car_tree('". $type ."', ". (int)$modificationId .")";
        
        $query = $this->tecdoc->query("call car_tree('". $type ."', ". (int)$modificationId .")", true);
        
//        echo '<pre>';
//        print_r($query); die;
        
        return $query->rows;
	}
}