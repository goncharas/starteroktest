<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelTecdocGroup extends Model {
	public function editStatus($group_id, $status) {
        $this->tecdoc->query("UPDATE prd SET status = '" . (int)$status . "', date_modified = NOW() WHERE id = '" . (int)$group_id . "'");
        
//		$this->cache->delete('product');
		
		return $group_id;
    }

	public function getGroups($data = array()) {
		$sql = "SELECT * FROM prd p";

        $where = $this->getFilterWhere($data);
        if (!empty($where)) {
            $sql .= ' WHERE '.$where;
        }

		$sort_data = array(
			'assemblygroupdescription',
			'description',
			'status',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY assemblygroupdescription ASC, description";
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

	public function getTotalGroups($data = array()) {
		$sql = "SELECT COUNT(id) AS total FROM prd ";

        $where = $this->getFilterWhere($data);
        if (!empty($where)) {
            $sql .= ' WHERE '.$where;
        }

		$query = $this->tecdoc->query($sql);

		return $query->row['total'];
	}
    
    private function getFilterWhere($data) {
//        $where = " assemblygroupdescription <> ''";
        $where = "";
		if (!empty($data['filter_name'])) {
			$where .= (!empty($where) ? ' AND ':''). " LOWER(description) LIKE '%" . $this->tecdoc->escape(mb_strtolower($data['filter_name']), 'UTF-8') . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$where .= (!empty($where) ? ' AND ':''). " status = '" . (int)$data['filter_status'] . "'";
		}
        
        return $where;
    }
}
