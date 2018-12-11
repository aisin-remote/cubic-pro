@extends('app')
@section('content')

<!-- v3.1 by Ferry, 20150903, Integrate framework -->

                    <!-- /Content of Items Shown -->
                    <div class="row">

                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>List of Capex For Open / Close Action</h2>
                                    <ul class="nav navbar-right panel_toolbox">
                                        <li>
                                            <div class="btn-group pull-right">
                                                <button type="submit" class="btn btn-info" onclick="closing(0)">Open Budget</button>
                                                <button type="submit" class="btn btn-warning" onclick="closing(1)">Close Budget</button>
                                            </div>
                                        </li>
                                        <li style="float: right"><a href="#" class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </li>   
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <form>
                                        <table id="data_table" class="table table-bordered responsive-utilities jambo_table">
                                            <thead>
                                                <tr>
                                                    <th>Bdgt. Number</th>
                                                    <th>Equipment Name</th>
                                                    <th>Bdgt. Plan</th>
                                                    <th>Bdgt. Used</th>
                                                    <th>Bdgt. Remaining</th>
                                                    <th>Plan GR</th>
                                                    <th>Status</th>
                                                    <th>Closing</th>
                                                    <th><input type="checkbox" name="checkall" id="checkall" onclick="checkAll(this);"></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </form>
                                </div>
                                <!-- x_content -->
                            </div>
                            <!-- x_panel -->
                        </div>
                    </div>
                    <!-- /Content of Items Shown -->

<!-- End of v3.1 by Ferry, 20150903, Integrate framework -->

