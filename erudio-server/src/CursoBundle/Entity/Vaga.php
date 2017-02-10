<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *    @author Municipio de Itajaí - Secretaria de Educação - DITEC         *
 *    @updated 30/06/2016                                                  *
 *    Pacote: Erudio                                                       *
 *                                                                         *
 *    Copyright (C) 2016 Prefeitura de Itajaí - Secretaria de Educação     *
 *                       DITEC - Diretoria de Tecnologias educacionais     *
 *                        ditec@itajai.sc.gov.br                           *
 *                                                                         *
 *    Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
 *    modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
 *    publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
 *    Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
 *                                                                         *
 *    Este programa  é distribuído na expectativa de ser útil, mas SEM     *
 *    QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
 *    ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
 *    sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
 *                                                                         *
 *    Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
 *    junto  com  este  programa. Se não, escreva para a Free Software     *
 *    Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
 *    02111-1307, USA.                                                     *
 *                                                                         *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

namespace CursoBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use JMS\Serializer\Annotation as JMS;
use CoreBundle\ORM\AbstractEditableEntity;
use CursoBundle\Entity\SolicitacaoVaga;

/**
* @ORM\Entity
* @ORM\Table(name = "edu_vaga")
*/
class Vaga extends AbstractEditableEntity {
        
    /**        
    * @JMS\Groups({"LIST"})
    * @ORM\Column(nullable = false, name = "turma_id") 
    * @ORM\OneToOne(targetEntity="Turma", mappedBy="id")
    */
    private $turma;
    
    /**        
    * @JMS\Groups({"LIST"})
    * @ORM\Column(nullable = true, name="solicitacao_vaga_id")
    * @ORM\OneToOne(targetEntity="SolicitacaoVaga", mappedBy="id")
    */
    private $solicitacaoVaga = null;
    
    /** 
    * @JMS\Groups({"LIST"})
    * @ORM\Column(nullable = true, name = "enturmacao_id") 
    */
    private $enturmacao = null;
    
    function getTurma() {
        return $this->turma;
    }

    function getSolicitacaoVaga() {
        return $this->solicitacaoVaga;
    }

    function getEnturmacao() {
        return $this->enturmacao;
    }

    function setTurma($turma) {
        $this->turma = $turma;
    }

    function setSolicitacaoVaga($solicitacao) {
        $this->solicitacaoVaga = $solicitacao;
    }

    function setEnturmacao($enturmacao) {
        $this->enturmacao = $enturmacao;
    }    
}