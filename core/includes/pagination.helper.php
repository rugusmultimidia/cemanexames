<?php


class Pagination extends Model {
    
     //var $limit = '0,5';
	 
	 public $perPage, $maxPages, $currentPage, $lines, $query_string = '';
    
    public function setPaginate($count, $linksPerPage, $maxLinks, $currentPage){
        
         $this->perPage = $linksPerPage;
         $this->maxPages= $maxLinks;
         $this->currentPage = $currentPage;
		 $this->lines = $count;
         
    }

    
	public function link($link) {		
		
		$this->link = $link;
		
	}
	
	public function getLimit() {
		
		if(isset($this->currentPage)) {
            
                    $this->currentPage = $this->currentPage; 
                
            } else {
                    
                    $this->currentPage = 1;          
		}
		
		$this->initPage = ($this->currentPage * $this->perPage) - $this->perPage;
		
		$this->limit = $this->initPage .",". $this->perPage;
		
		return $this->limit;
		
		
	}
    
	
	
	public function defineQryString($qs) {
	
		$this->query_string = $qs;
		
	}
	

        
    public function paginate() {
    
        
        
        if(isset($this->currentPage)) {
            
                    $this->currentPage = $this->currentPage; 
                
            } else {
                    
                    $this->currentPage = 1;          
		}
        

        $pagination_html = '';
		
        $this->initPage = ($this->currentPage * $this->perPage) - $this->perPage;
        
        
        $this->paginate = $this->initPage . "," . $this->perPage;

       if($this->lines < $this->perPage) {
		   $this->numberPages = 1;
	   } else {		      
		   $this->numberPages = ceil($this->lines / $this->perPage);		     	
	   }
        
        $pagination_html .='<nav><ul class="pagination">
		<li>
		  <a href="'.$this->link."/1/".$this->query_string.'" aria-label="Previous">
			<span aria-hidden="true">&laquo;</span>
		  </a>
		</li>';
        
         for($i = $this->currentPage - $this->maxPages; $i <= $this->currentPage-1; $i++) {
             
             if($i > 0) {             
                     
                    $pagination_html .= "<li><a href='".$this->link."/".$i."/".$this->query_string."'>".$i."</a></li>";
                        
             }
                       
         }
             
         $pagination_html .= "<li class='active'><a href='javascript:void(0)'>" .$i ."</a></li>" ;          
          
         for($i = $this->currentPage+1; $i <= $this->currentPage+$this->maxPages; $i++) {

             if($i <= $this->numberPages) {
                
                 $pagination_html .=  "<li><a href='".$this->link."/".$i."/".$this->query_string."'>".$i."</a></li>";
                
            }         

         }
         
		 $pagination_html .= 
		 '<li>
		  <a href='.$this->link."/".$this->numberPages."/".$this->query_string.' aria-label="Next">
			<span aria-hidden="true">&raquo;</span>
		  </a>
		</li></ul></nav>';
		 
		 
         print $pagination_html;
    
    
    }
    
    
}






?>
