
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class sellin extends Conectar{

	public function getsellin($fechai,$fechaf,$marca,$tipo){

		 //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		 //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();


 if($tipo=='f'){



	//QUERY
		$sql = "SELECT DISTINCT(SAITEMCOM.CodItem) AS coditem,

		SUM(CASE WHEN TipoCom = 'H' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
        SUM(CASE WHEN TipoCom = 'H' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END) AS compras,
        SUM(CASE WHEN TipoCom = 'I' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
        SUM(CASE WHEN TipoCom = 'I' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END) AS devol,

       (SUM(CASE WHEN TipoCom = 'H' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
        SUM(CASE WHEN TipoCom = 'H' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END)) -

       (SUM(CASE WHEN TipoCom = 'I' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
        SUM(CASE WHEN TipoCom = 'I' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END)) AS total,

		(SELECT SAPROD.Descrip FROM SAPROD WHERE SAPROD.CodProd = SAITEMCOM.CodItem) AS producto,
		(SELECT SAPROD.Marca FROM SAPROD WHERE SAPROD.CodProd = SAITEMCOM.CodItem) AS marca


		FROM SAITEMCOM INNER JOIN SAPROD ON SAITEMCOM.CodItem = SAPROD.CodProd where
		DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMCOM.FechaE)) between ? AND ? ";

		if(!hash_equals("-", $marca))
		{
			$sql .= " AND saprod.marca = '$marca'";
		}

		$sql .= " AND (TipoCom = 'H' OR TipoCom = 'I')  GROUP BY (CodItem)";

       
        

    }else{

             if($tipo=='n'){



				//QUERY
				$sql = "SELECT DISTINCT(SAITEMCOM.CodItem) AS coditem,

				SUM(CASE WHEN TipoCom = 'J' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
				SUM(CASE WHEN TipoCom = 'J' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END) AS compras,
				SUM(CASE WHEN TipoCom = 'K' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
				SUM(CASE WHEN TipoCom = 'K' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END) AS devol,

			    (SUM(CASE WHEN TipoCom = 'J' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
				SUM(CASE WHEN TipoCom = 'J' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END)) -

			    (SUM(CASE WHEN TipoCom = 'K' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
				SUM(CASE WHEN TipoCom = 'K' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END)) AS total,

				(SELECT SAPROD.Descrip FROM SAPROD WHERE SAPROD.CodProd = SAITEMCOM.CodItem) AS producto,
				(SELECT SAPROD.Marca FROM SAPROD WHERE SAPROD.CodProd = SAITEMCOM.CodItem) AS marca


				FROM SAITEMCOM INNER JOIN SAPROD ON SAITEMCOM.CodItem = SAPROD.CodProd where
				DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMCOM.FechaE)) between ? AND ? ";

				if(!hash_equals("-", $marca))
				{
					$sql .= " AND saprod.marca = '$marca'";
				}

				$sql .= " AND (TipoCom = 'J' OR TipoCom = 'K')  GROUP BY (CodItem)";

               
                

            }else{

                 if($tipo=='Todos'){


					$sql = "SELECT DISTINCT(SAITEMCOM.CodItem) AS coditem,TipoCom as tipodoc,

						SUM(CASE WHEN TipoCom = 'H' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
						SUM(CASE WHEN TipoCom = 'H' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END) AS compras,
						SUM(CASE WHEN TipoCom = 'I' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
						SUM(CASE WHEN TipoCom = 'I' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END) AS devol,

						SUM(CASE WHEN TipoCom = 'J' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
						SUM(CASE WHEN TipoCom = 'J' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END) AS compras_notas,

						SUM(CASE WHEN TipoCom = 'K' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
						SUM(CASE WHEN TipoCom = 'K' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END) AS devol_notas,

					   (SUM(CASE WHEN TipoCom = 'J' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
						SUM(CASE WHEN TipoCom = 'J' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END))+
						SUM(CASE WHEN TipoCom = 'H' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
						SUM(CASE WHEN TipoCom = 'H' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END) -

					    (SUM(CASE WHEN TipoCom = 'K' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
						SUM(CASE WHEN TipoCom = 'K' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END))+
						SUM(CASE WHEN TipoCom = 'I' AND Esunid = '0' THEN SAITEMCOM.Cantidad ELSE 0 END) +
						SUM(CASE WHEN TipoCom = 'I' AND Esunid = '1' THEN COALESCE(SAITEMCOM.Cantidad/NULLIF(SAPROD.CantEmpaq, 0), 0) ELSE 0 END) AS total,

						(SELECT SAPROD.Descrip FROM SAPROD WHERE SAPROD.CodProd = SAITEMCOM.CodItem) AS producto,
						(SELECT SAPROD.Marca FROM SAPROD WHERE SAPROD.CodProd = SAITEMCOM.CodItem) AS marca


						FROM SAITEMCOM INNER JOIN SAPROD ON SAITEMCOM.CodItem = SAPROD.CodProd where
						DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMCOM.FechaE)) between ? AND ? ";

						if(!hash_equals("-", $marca))
						{
							$sql .= " AND saprod.marca = '$marca'";
						}

						$sql .= " AND (TipoCom = 'H' OR TipoCom = 'I' OR TipoCom = 'J' OR TipoCom = 'K') GROUP BY CodItem , TipoCom";

                    
                    }

                }


            }


		 //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->bindValue(1,$fechai);
		$sql->bindValue(2,$fechaf);
       /* if(!hash_equals("-", $marca))
            $sql->bindValue(3,$marca);*/

		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	}



public function getfechasellin($fechai,$fechaf,$marca,$producto, $tipo){

		 //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		 //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();

 		//QUERY
		$sql = "SELECT DISTINCT(SAITEMCOM.CodItem) AS coditem , FechaE
		FROM SAITEMCOM INNER JOIN SAPROD ON SAITEMCOM.CodItem = SAPROD.CodProd where
		DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMCOM.FechaE)) between '$fechai' AND '$fechaf' ";

		if(!hash_equals("-", $marca))
		{
			$sql .= " AND saprod.marca = '$marca'";
		}




if($tipo=='f'){



       $sql .= " AND (TipoCom = 'H' OR TipoCom = 'I')  and CodItem='$producto'";

        

    }else{

             if($tipo=='n'){



			$sql .= " AND (TipoCom = 'J' OR TipoCom = 'K')  and CodItem='$producto'";

                

            }else{

                 if($tipo=='Todos'){

						$sql .= " AND (TipoCom = 'H' OR TipoCom = 'I' OR TipoCom = 'J' OR TipoCom = 'K')  and CodItem='$producto'";

                    
                    }

                }


            }

		
		 //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		/*$sql->bindValue(1,$fechai);
		$sql->bindValue(2,$fechaf);*
        /**if(!hash_equals("-", $marca))
            $sql->bindValue(3,$marca);*/
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	}




}

