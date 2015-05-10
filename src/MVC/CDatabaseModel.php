<?php
namespace Anax\MVC;

class CDatabaseModel implements \Anax\di\IInjectionAware
{
	use \Anax\DI\TInjectable;

	/**
	 *Get table name
	 */
	public function getSource()
	{
		return strtolower(implode('', array_slice(explode('\\', get_class($this)), -1)));
	}
	/**
	* Find and return all
	*
	* @return array
	*/
	public function findAll()
	{
		$this->db->select()
				 ->from($this->getSource());

		$this->db->execute();
		$this->db->setFetchModeClass(get_class($this));
		return $this->db->fetchAll();
	}

	/**
	 * Find and return specific
	 *
	 * @return object
	 */
	protected function find($value, $key)
	{
		$this->db->select()
				 ->from($this->getSource())
				 ->where($key.'=?');

		$this->db->execute([$value]);
		//return $this->db->fetchAll();
		return $this->db->fetchInto($this);
	}

	/**
	* Get object properties
	*
	* @return array
	*/
	public function getProperties()
	{
		$properties =get_object_vars($this);
		unset($properties['di']);
		unset($properties['db']);

		return $properties;
	}

	public function setProperties($properties)
	{
		if(!empty($properties)){
			foreach($properties as $key => $val){
				$this->$key =$val;
			}
		}
	}

	protected function save($values, $key)
	{
		$this->setProperties($values);
		$values =$this->getProperties();

		if(isset($values[$key])){
			return $this->update($values, $key);
		} else {
			return $this->create($values);
		}
	}

	private function create($values)
	{
		$keys =array_keys($values);
		$values =array_values($values);

		$this->db->insert (
			$this->getSource(),
			$keys
		);

		$res =$this->db->execute($values);

		$this->id =$this->db->lastInsertId();

		return $res;
	}

	protected function update($values, $key)
	{
		$val =$values[$key];
		unset($values[$key]);
		$keys =array_keys($values);
		$values =array_values($values);

	//	$values[] =$val;
		$this->db->update(
			$this->getSource(),
			$keys,
			$values,
			$key.'='.$val
		);

		$this->db->execute($values);
	}

	protected function delete($value, $key)
	{
		$this->db->delete(
			$this->getSource(),
			$key.'=?'
		);
		return $this->db->execute([$value]);
	}

	/****************************************
	******** Query building methods *********
	****************************************/

	public function query($columns ='*')
	{
		$this->db->select($columns)
				 ->from($this->getSource());

		return $this;
	}

	public function where($condition)
	{
		$this->db->where($condition);

		return $this;
	}

	public function andWhere($condition)
	{
		$this->db->andWhere($condition);

		return $this;
	}

	public function execute($params =[])
	{
		$this->db->execute($this->db->getSQL(), $params);
		$this->db->setFetchModeClass(get_class($this));

		return $this->db->fetchAll();
	}
}