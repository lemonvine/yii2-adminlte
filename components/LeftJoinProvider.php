<?php
namespace lemon\components;

use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;
use yii\base\InvalidConfigException;

/**
 * 含合计的DataProvider
 * @author shaodd
 *
 */
class LeftJoinProvider extends ActiveDataProvider
{
	
	/**
	 * {@inheritdoc}
	 */
	protected function prepareTotalCount()
	{
		if (!$this->query instanceof QueryInterface) {
			throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
		}
		$query = clone $this->query;
		$query->join=[];
		return (int) $query->limit(-1)->offset(-1)->orderBy([])->count('*', $this->db);
	}
}

