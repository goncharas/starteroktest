<?php
class ModelSearchPage extends Model {
    
    public function getPageInfo($url, $datatype='') {
        
        $sql = "SELECT A.*, B.* FROM ete_markpage A "
                . "INNER JOIN ete_markpage_description B ON (A.id = B.markpage_id) "
                . "WHERE A.url = '". $this->db->escape($url) ."' "
                    . "AND B.language_id = '" . (int)$this->config->get('config_language_id') . "' "
                    . (!empty($datatype) ? " AND A.datatype='". $this->db->escape($datatype) ."'" : '');
        
//        echo $sql;

		$query = $this->db->query($sql);

		return $query->row;
	}
    
    public function getPageTemplateInfo($datatype) {
        
        $sql = "SELECT * FROM ete_meta_pattern "
                . "WHERE mp_datatype = '". $this->db->escape($datatype) ."'"
                . "AND mp_language_id = '" . (int)$this->config->get('config_language_id') . "' ";

		$query = $this->db->query($sql);

        $row = $query->row;
        
        if (!empty($row)) {
            return [
                'meta_title' => $row['mp_title'],
                'meta_description' => $row['mp_description'],
                'is_template' => true,
            ];
        }
        
		return $row;
	}
}