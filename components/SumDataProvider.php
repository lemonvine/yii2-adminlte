<?php
namespace lemon\components;

use yii\data\ActiveDataProvider;

/**
 * 含合计的DataProvider
 * @author shaodd
 *
 */
class SumDataProvider extends ActiveDataProvider
{
	public $sumMoney;
	public $sumCaption='合计';
	
}

