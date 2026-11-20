<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distance extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */


	public $node, $maxLength;

	/**
	 *Constructor of this class. It will call loadData method
	 * whenever object is created.
	 */
	public  function RouteDijkstra()
	{
		// $sitePath		= Configuration::getValueFromName('absolute_site_path');
		// $dataFile = $sitePath . "assets/distance/net.txt";

        // $file_path =file_path
        $dataFile = public_path('assets/distance/net.txt');
        // dd($dataFile);

		$this->maxLength = 10000;
		#print $dataFile;

		return $this->loadData($dataFile);
		#print "ok";

	}


	/*
			* It will initialize road map sets on node properties of calss.
			*
			* @param $dataFile string
			*/
	public  function loadData($dataFile = NULL)
	{

		// file_exists($dataFile) or die("Unable to load File : ".$dataFile);


		$data = file($dataFile);


		$this->node = array();


		for ($i = 0; $i < count($data); $i++) {
			$tmp_data = explode(",", trim($data[$i]));
			$nodeName = trim($tmp_data[0]);

			$this->node[$nodeName]['nodeName'] = $nodeName;
			$this->node[$nodeName]['DistFromOrigin'] = $this->maxLength;
			$this->node[$nodeName]['RouteFromOrigin'] = NULL;

			for ($j = 1; $j < count($tmp_data); $j++) {
				$subNode = trim($tmp_data[$j]);
				$subNodeLength = trim($tmp_data[$j + 1]);
				$this->node[$nodeName]['subNode'][$subNode] = $subNodeLength;
				$j = $j + 1;
			}
		}
	}

	/*
			* It will create html select tag
			*
			* @param $selectName string
			* @param $optSelect boolean
			* @returns string
			*/
	public  function getSelectHtml($selectName = NULL, $test = null, $optSelect = "FALSE")
	{
		//print_r($this->node);exit;
		$selectedNode = $selectName;

		$Output = "<select name=\"" . $selectName . "\" class=\"form-control\">";
		if ($optSelect == "TRUE")
			$Output .= "<option value=\"\">--Select--</option>";
		foreach ($this->node as $nodeName => $nodeArray) {
			$sel = $nodeName == $selectedNode ? "selected" : NULL;
			$Output .= "<option value=\"" . $nodeName . "\"" . $sel . ">" . $nodeName . "</option>";
		}
		$Output .= "</select>";
		return $Output;
	}



	/*
			* This is the heart of class. This is where actual process is done.
			* If you want to understand this code focus in this method.
			* This method is totally based on Dijkstra Algorithm sudo code.
			* if you want to know more about Dijkstra Algorithm to through
			* URL : http://en.wikipedia.org/wiki/Dijkstra's_algorithm
			*
			* @param $origin string
			* @param $destination string
			* @returns array
			*/
	public  function findDistanceRoute($origin, $destination, $test = null)
	{
		$output = array();
		$nodeInner = $this->node; //for total sum

		$totalLooped = 0;
		$this->node[$origin]['DistFromOrigin'] = 0;
		$this->node[$origin]['RouteFromOrigin'] = $this->node[$origin]['nodeName'];
		$sum = $this->maxLength * count($this->node);

		do {
			$previousSum = $sum;

			foreach ($this->node as $nodeName => $nodeArray) {
				if (is_array($nodeArray))
					foreach ($nodeArray['subNode'] as $subNodeName => $length) {

						$d = $this->node[$nodeName]['DistFromOrigin'] + $length;
						$r = $this->node[$nodeName]['RouteFromOrigin'] . " - " . $subNodeName;

						if ($d < $this->node[$subNodeName]['DistFromOrigin']) {
							$this->node[$subNodeName]['DistFromOrigin'] = $d;
							$this->node[$subNodeName]['RouteFromOrigin'] = $r;
						}
					}
				$sum = 0;
				foreach ($nodeInner as $nN => $nA) {
					$sum += $this->node[$nN]['DistFromOrigin'];
				}
			}
		} while ($sum < $previousSum);

		$output['Distance'] = $this->node[$destination]['DistFromOrigin'];
		$output['RouteFromOrigin'] = $this->node[$destination]['RouteFromOrigin'];
		return $output;
	}
}
