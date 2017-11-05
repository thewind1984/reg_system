<?php	namespace Helper;

	class Pagination {
		
		/**
		 * Renders array of pagination links
		 * @param pages integer		total pages
		 * @param page integer		current page
		 * @param pre integer		how many page links to show before 'hole'
		 */
		public static function render($pages, $page, $pre = 8){
			$pageItems = [];
			
			if ($page > $pre+1) {
				$pageItems[] = ['num' => 1, 'link' => true];
				
				if ($page-$pre > 2) {
					$pageItems[] = ['num'  =>  '...', 'link'  =>  false];
				}
			}
			
			$preStart = ($_pre = $page-$pre) >= 1 ? $_pre : 1;
			
			for ($pg=$preStart; $pg<$page; $pg++)
				$pageItems[] = ['num' => $pg, 'link' => true];
			
			$pageItems[] = ['num' => $page, 'link' => true, 'active' => true];
			$preFinish = ($_pre = $page+$pre) <= $pages ? $_pre : $pages;
			
			for ($pg=$page+1; $pg<=$preFinish; $pg++)
				$pageItems[] = ['num' => $pg, 'link' => true];
			
			if ($preFinish < $pages) {
				if ($preFinish < $pages-1) {
					$pageItems[] = ['num' => '...', 'link' => false, 'space' => true];
				}
				
				$pageItems[] = ['num' => $pages, 'link' => true, 'next' => true];
			}
			
			return $pageItems;
		}
		
	}
	
?>