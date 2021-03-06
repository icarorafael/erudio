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

namespace CursoBundle\Service;

use Doctrine\ORM\QueryBuilder;
use CoreBundle\ORM\AbstractFacade;
use CursoBundle\Entity\Vaga;
use CursoBundle\Entity\Turma;
use CoreBundle\ORM\Exception\IllegalUpdateException;

class TurmaFacade extends AbstractFacade {
    
    private $vagaFacade;
    
    function removerAgrupamento(Turma $turma) {
        $turma->setAgrupamento(null);
        $this->orm->getManager()->flush();
    }
    
    function getEntityClass() {
        return 'CursoBundle:Turma';
    }
    
    function setVagaFacade($vagaFacade) {
        $this->vagaFacade = $vagaFacade;
    }
    
    function queryAlias() {
        return 't';
    }
    
    function parameterMap() {
        return array (
            'nome' => function(QueryBuilder $qb, $value) {
                $qb->andWhere('t.nome LIKE :nome')->setParameter('nome', '%' . $value . '%');
            },
            'apelido' => function(QueryBuilder $qb, $value) {
                $qb->andWhere('t.apelido LIKE :apelido')->setParameter('apelido', '%' . $value . '%');
            },        
            'status' => function(QueryBuilder $qb, $value) {
                $qb->andWhere('t.status = :status')->setParameter('status', $value);
            },                    
            'encerrado' => function(QueryBuilder $qb, $value) {
                $operator = $value ? '=' : '<>';
                $qb->andWhere("t.status ${operator} :encerrado")->setParameter('encerrado', Turma::STATUS_ENCERRADO);
            },
            'curso' => function(QueryBuilder $qb, $value) {
                $qb->join('etapa.curso', 'curso')                   
                   ->andWhere('curso.id = :curso')->setParameter('curso', $value);
            },
            'etapa' => function(QueryBuilder $qb, $value) {
                $qb->andWhere('etapa.id = :etapa')->setParameter('etapa', $value);
            },
            'quadroHorario' => function(QueryBuilder $qb, $value) {
                $qb->join('t.quadroHorario', 'quadroHorario')
                    ->andWhere('quadroHorario.id = :quadroHorario')->setParameter('quadroHorario', $value);
            },
            'agrupamento' => function(QueryBuilder $qb, $value) {
                $qb->join('t.agrupamento', 'agrupamento')
                    ->andWhere('agrupamento.id = :agrupamento')->setParameter('agrupamento', $value);
            },
            'unidadeEnsino' => function(QueryBuilder $qb, $value) {
                $qb->join('t.unidadeEnsino', 'unidadeEnsino')
                   ->andWhere('unidadeEnsino.id = :unidadeEnsino')
                   ->setParameter('unidadeEnsino', $value);
            }
        );
    }    
    
    protected function prepareQuery(QueryBuilder $qb, array $params){
        $qb->join('t.etapa', 'etapa')->orderBy('etapa.ordem');
    }
        
    protected function beforeRemove($turma) {
        $turma->setStatus(Turma::STATUS_ENCERRADO);
    }
    
    protected function afterCreate($turma) {
        $this->gerarVagas($turma, $turma->getLimiteAlunos());
    }
    
    protected function afterUpdate($turma) {
        $numeroVagas = $turma->getVagas()->count();
        if ($numeroVagas < $turma->getLimiteAlunos()) {
            $this->gerarVagas($turma, $turma->getLimiteAlunos() - $numeroVagas);
        } else if ($numeroVagas > $turma->getLimiteAlunos()) {
            throw new IllegalUpdateException(
                IllegalUpdateException::FINAL_STATE, 
                'Operação não permitida, não é possível diminuir a quantidade de vagas de uma turma'
            );
        }
    }
    
    private function gerarVagas(Turma $turma, $quantidade) {
        for ($i = 0; $i < $quantidade; $i++) {
            $vaga = new Vaga();
            $vaga->setTurma($turma);
            $this->vagaFacade->create($vaga);
        }
    }

}
