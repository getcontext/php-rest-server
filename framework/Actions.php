<?php
namespace Framework;
/**
 *
 * @author Andrzej Salamon <andrzej.salamon@gmail.com>
 * @package framework
 */
interface Actions
{
    public function execute(Request $request);
}