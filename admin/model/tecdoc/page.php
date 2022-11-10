<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelTecdocPage extends Model {
	public function getPages($data = array()) {
		$sql = "SELECT * FROM ete_markpage A "
                . "INNER JOIN ete_markpage_description B ON (A.id = B.markpage_id) AND B.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
                //. "WHERE ";

        $where = $this->getFilterWhere($data);
        if (!empty($where)) {
            $sql .= ' WHERE '.$where;
        }

		$sort_data = array(
			'datatype',
			'url',
			'meta_title',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY url";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalPages($data = array()) {
		$sql = "SELECT COUNT(id) AS total FROM ete_markpage ";

        $where = $this->getFilterWhere($data);
        if (!empty($where)) {
            $sql .= ' WHERE '.$where;
        }

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
    
    private function getFilterWhere($data) {
        $where = "";
		if (!empty($data['filter_url'])) {
			$where .= (!empty($where) ? ' AND ':''). " LOWER(url) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_url']), 'UTF-8') . "%'";
		}
        
        return $where;
    }
    
    public function getPage($pageId) {
		$sql = "SELECT A.*, B.* FROM ete_markpage A "
                . "LEFT JOIN ete_markpage_description B ON (A.id = B.markpage_id) "
                . "WHERE A.id = ". (int)$pageId ." AND B.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$query = $this->db->query($sql);

		return $query->row;
	}
    
    public function getPageLangData($pageId) {
		$pageLangData = array();

		$query = $this->db->query("SELECT * FROM ete_markpage_description WHERE markpage_id = '" . (int)$pageId . "'");

		foreach ($query->rows as $result) {
			$pageLangData[$result['language_id']] = array(
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
			);
		}

		return $pageLangData;
	}
    
    public function addPage($data) {
		$this->db->query("INSERT INTO ete_markpage SET "
                . "datatype = '" . $this->db->escape($data['datatype']) . "', "
                . "url = '" . $this->db->escape($data['url']) . "', "
                . "date_inserted = NOW(), date_updated = NOW()");

		$pageId = $this->db->getLastId();

		foreach ($data['page_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO ete_markpage_description SET "
                . "markpage_id = '" . (int)$pageId . "', language_id = '" . (int)$language_id . "', "
                . "description = '" . $this->db->escape($value['description']) . "', "
                . "meta_title = '" . $this->db->escape($value['meta_title']) . "', "
                . "meta_description = '" . $this->db->escape($value['meta_description']) . "', "
                . "meta_keyword = '" . $this->db->escape(!empty($value['meta_keyword']) ? $value['meta_keyword'] : '') . "'");
		}

//		$this->cache->delete('product');

		return $pageId;
	}
    
    public function editPage($pageId, $data) {
		$this->db->query("UPDATE ete_markpage SET "
                    . "datatype = '" . $this->db->escape($data['datatype']) . "', "
                    . "url = '" . $this->db->escape($data['url']) . "', "
                    . "date_updated = NOW() "
                . "WHERE id = ". (int)$pageId);

        $this->db->query("DELETE FROM ete_markpage_description WHERE markpage_id = '" . (int)$pageId . "'");

		foreach ($data['page_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO ete_markpage_description SET "
                . "markpage_id = '" . (int)$pageId . "', language_id = '" . (int)$language_id . "', "
                . "description = '" . $this->db->escape($value['description']) . "', "
                . "meta_title = '" . $this->db->escape($value['meta_title']) . "', "
                . "meta_description = '" . $this->db->escape($value['meta_description']) . "', "
                . "meta_keyword = '" . $this->db->escape(!empty($value['meta_keyword']) ? $value['meta_keyword'] : '') . "'");
		}

//		$this->cache->delete('product');
	}
    
    public function deletePage($pageId) {
echo '<br>delete'.$pageId;
        $this->db->query("DELETE FROM ete_markpage_description WHERE markpage_id = '" . (int)$pageId . "'");
        $this->db->query("DELETE FROM ete_markpage WHERE id = '" . (int)$pageId . "'");

//		$this->cache->delete('product');
	}
}
