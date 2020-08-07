<?php 
class Search_model extends Model {
	function Search_model()
	{
		
	  // 呼叫模型(Model)的建構函數
        parent::Model();
	}
    function deleteIndex($mainID,$type)
	{
		 
        $this->db->where('mainID',$mainID);
        $this->db->where('type',$type);
        $query=$this->db->get('pos_invert_index');
		if($query->num_rows()>0)
        {
            $data  = $query->result_array();
       
            foreach($data as $row)
            {
                  
                $termID = $row['termID'];
                $this->db->where('termID',$termID);
			     $query=$this->db->get('pos_term_index');
                if($query->num_rows()>0)
                {
                   
				    $this->db->where('termID',$termID);
				    $this->db->set('num', 'num-'.$query->num_rows(), FALSE);
				    $this->db->update('pos_term_index');
              
                
                }
            }
         
			$this->db->where('type',$type);
            $this->db->where('mainID',$mainID);
			$this->db->delete('pos_invert_index');
   
            
            
            
        }
        
        
      
		
		
		
	}
	
	

	function updateIndex($string,$isENG,$mainID,$type)
	{
		
		if(!$isENG)
		{
			$name = explode (';',$string);
			$j = 0 ;
			foreach($name as $row)
			{
				$num = mb_strlen($row,'utf8');
				for($i=0;$i<$num;$i++)
				{	
					$term = mb_substr($row,$i,1,'utf8');
					
					$term = htmlspecialchars($term,ENT_QUOTES );
					
					if($term !='&#59'&&$term!=' ')
					{
						if(!isset($termArray[$term]))
						{
							$termArray[$term]['term'] = $term;
							$termArray[$term]['index'] = $j;
							$termArray[$term]['num'] = 1;
						}
						else if($termArray[$term]['index'] ==$j)
						{
							$termArray[$term]['index'] = $j;
							$termArray[$term]['num']++;
							
						}
						else
						{
							$termArray[$term]['index'] = $j;
						}
					}
				}
				$j++;
				
			}
		}
		else
		{
			$name = explode (' ',$string);
			foreach($name as $row)
			{
				if(!isset($termArray[$row]))
				{
					$termArray[$row]['term'] = $row;
					$termArray[$row]['num'] = 1;
				}
				else
				{
					$termArray[$row]['num']++;
					
				}
			}
		}
		
		foreach($termArray as $row)	
		{
			if($row['term'] !='&#59'&&$row['term']!=' ')
			{
			
				$this->db->where('term',$row['term']);
				$query=$this->db->get('pos_term_index');
				if($query->num_rows()==0)
				{
					
					$this->db->insert('pos_term_index',array('term'=>$row['term']));
					$termID = $this->db->insert_id();
					
					
				}
				else
				{
					$data = $query->row_array();
					$termID = $data['termID'];
					
				}
		
				$this->db->where('termID',$termID,FALSE);
				$this->db->where('type',$type);
                $this->db->where('mainID',$mainID);
				$query=$this->db->get('pos_invert_index');
				if($query->num_rows()==0)
				{
					$this->db->insert('pos_invert_index',array('termID'=>$termID,'mainID'=>$mainID,'times'=>$row['num']));
				}
				else
				{
					$this->db->where('termID',$termID);
					$this->db->where('type',$type);
                    $this->db->where('mainID',$mainID);
					$this->db->set('times', 'times+'.$row['num'], FALSE);			
					$this->db->update('pos_invert_index');
						
				}
						
					$this->db->where('termID',$termID);				
					$this->db->set('num', 'num+'.$row['num'], FALSE);
					$this->db->update('pos_term_index');
				}	 
			
		}
	
		return;
	}

}

?>
