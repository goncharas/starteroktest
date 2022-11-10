<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerTecdocCategory extends Controller {
    private $path;
    
    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->connectToTecdocDb();
    }
    
	public function index($args) {
		$this->load->model('tecdoc/category');
        
        if (isset($args['modificationId'])) {
            $modificationId = $args['modificationId'];
        } else {
            
        }
        if (isset($args['type'])) {
            $type = $args['type'];
        } else {
            
        }
        if (isset($args['path'])) {
            $this->path = $args['path'];
        } else {
            
        }
        if (isset($args['tecdocCategoryId'])) {
            $data['tecdocCategoryId'] = $args['tecdocCategoryId'];
        } else {
            $data['tecdocCategoryId'] = '';
        }

        $categories = $this->model_tecdoc_category->getCategoriesByModification($modificationId, $type);
        
//        echo '<pre>';
//        print_r($categories); die;
        
        $nodes = array();
        foreach($categories as $node) {
            $nodes[ $node['tree_parentid'] ][] = $node;
//                    array(
//                'id' => $node['tree_id'],
//                'name' => $node['tree_description'],
//                'description' => $node['prd_description'],
//            );
        }
        
        $data['categories'] = array();
        
        if (isset($nodes[0])) {
            $data['categories'] = $this->buildMenuStructure($nodes[0], $nodes);
            return $this->load->view('search/menu', $data);
        } else {
            return '';
        }
	}
    
    private function connectToTecdocDb() {
        // модель tecdoc работает с объектом подключения tecdoc
        $tecdocDb = new DB(DB_DRIVER_TECDOC, DB_HOSTNAME_TECDOC, DB_USERNAME_TECDOC, DB_PASSWORD_TECDOC, DB_DATABASE_TECDOC);
        $this->registry->set('tecdoc', $tecdocDb);
    }
    
    private function buildMenuStructure($levelNodes, $allNodes) {
        $menu = array();
        
        foreach($levelNodes as $key=>$node) {
            $menu[ $key ] = array(
                'id' => $node['tree_id'],
                'prd_id' => $node['prd_id'],
                'href' => (empty($node['prd_id']) ? '' : $this->path. '/'. $node['prd_id']), //javascript:openTcMenuNode();
                'name' => (empty($node['prd_id']) ? $node['tree_description'] : $node['prd_description']),
//                'prd_name' => $node['prd_description'],
            );
            if (isset($allNodes[$node['tree_id']])) {
                $menu[ $key ]['children'] = $this->buildMenuStructure($allNodes[$node['tree_id']], $allNodes);
            }
        }
        
        return $menu;
    }
}