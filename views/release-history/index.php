<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-08
 * Time: 14:15
 */
\app\assets\ReleaseHistoryIndex::register($this);
?>
<style type="text/css">
    #releaseStatus{
        display: inline;
        margin-left: 10%;
    }
    #releaseDate{
        display: inline;
        float: right;
    }
</style>

<div class="row">
    <div class="col-md-4" id="ReleaseHistoryList">


    </div>
    <div class="col-md-8">
        <div style="height: 50px;background-color:  #e6e6e6;">
            <div style="padding-top: 2%">
            <div style="display: inline" class="version"></div>
            <div style="display: none" class="uniqueId"></div>
            <div style="display: inline;float: right" class="datetime"></div>
            </div>
        </div>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default modifyButton">变更的配置</button>
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default allButton">全部的配置</button>
            </div>
        </div>
        <label class="tableTitle"></label>

        <div class="tableContainer">
<!--            表格预留位置-->
        </div>

    </div>
</div>
