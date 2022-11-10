<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelTecdocSupplier extends Model {

	public function getList($data = array()) {
		$sql = "SELECT * FROM suppliers";

        $where = $this->getFilterWhere($data);
        if (!empty($where)) {
            $sql .= ' WHERE '.$where;
        }

		$sort_data = array(
			'description_changed',
			'description',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY description";
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

		$query = $this->tecdoc->query($sql);

		return $query->rows;
	}

	public function getTotal($data = array()) {
		$sql = "SELECT COUNT(id) AS total FROM suppliers ";

        $where = $this->getFilterWhere($data);
        if (!empty($where)) {
            $sql .= ' WHERE '.$where;
        }

		$query = $this->tecdoc->query($sql);

		return $query->row['total'];
	}
    
    public function getSupplier($id) {
		$sql = "SELECT * FROM suppliers WHERE id = ". (int)$id;

		$query = $this->tecdoc->query($sql);

		return $query->row;
	}
    
    public function editSupplier($id, $data) {
		$this->tecdoc->query("UPDATE suppliers SET description_changed = '" . $this->db->escape($data['description_changed']) . "', date_modified = NOW() WHERE id = '" . (int)$id . "'");
    }
    
    private function getFilterWhere($data) {
//        $where = " assemblygroupdescription <> ''";
        $where = "";
		if (!empty($data['filter_description'])) {
			$where .= (!empty($where) ? ' AND ':''). " LOWER(description) LIKE '%" . $this->tecdoc->escape(mb_strtolower($data['filter_description']), 'UTF-8') . "%'";
		}

		if (isset($data['filter_description_changed']) && $data['filter_description_changed'] !== '') {
            if ($data['filter_description_changed'] == 1) {
                $where .= (!empty($where) ? ' AND ':''). " (description_changed <> '' AND description_changed IS NOT NULL)";
            } else {
                $where .= (!empty($where) ? ' AND ':''). " (description_changed = '' OR  description_changed IS NULL)";                
            }
		}
        
        return $where;
    }
}
