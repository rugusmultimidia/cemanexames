<?php 


	class SeoHelper {
		
		
		
		
		
		
		public function titleLink($x, $y, $title) {
			
			return $this->titleLink = strtolower(substr($x, $y, $title));
			

		}
		
		
		public function description($description) {
			
			$description_valid = str_replace("-", " ", $description);
			
			$this->description = $description_valid;
								
			return $this;
		}
		
		
		public function keywords($keywords) {
			
			$this->keywords = $keywords;
			
			return $this;
			
		}
		
		
		public function autor($autor) {
			
			$this->autor = $autor;
			
			return $this;
			
		}
		
		public function mailto($var) {
			
			$this->mailto = $var;
			
			return $this;
			
		}
		
		public function revisit($var) {
			
			$this->revisit = $var;
			
			return $this;
			
		}
		
		public function company($var) {
			
			$this->company = $var;
			
			return $this;
			
		}
		
		
		public function getMetaTags() {
			
			return $this->metaTags =
			'<meta name="title" content="'.$this->work.'" />
			<meta name="url" content="'.$this->url.'" />
			<meta name="description" content="'.$this->description.'" />
			<meta name="keywords" content="'.$this->keywords.'" />
			<meta name="autor" content="'.$this->autor.'" />
			<meta name="company" content="'.$this->company.'" />
			<meta name="revisit-after" content="'.$this->revisit.'" />
			<link rev="made" href="mailto:'.$this->mailto.'" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			
		}
		
		
		
		
		
		
	}





?>