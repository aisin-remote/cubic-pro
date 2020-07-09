@extends('app')
@section('content')

<!-- v3.1 by Ferry, 20150903, Integrate framework -->

                    <!-- /Content of Items Shown -->
                    <div class="row">

                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <table>
                                <tr>
                                    <th>Budget No</th>
                                    <th>: {{$capex->budget_no}}</th>
                                </tr>
                                <tr>
                                    <th>Budget Description</th>
                                    <th>: {{$capex->equipment_name}}</th>
                                </tr>
                                <tr>
                                    <th>Budget Plan | Remaining</th>
                                    <th>: {{$capex->BudgetPlanFormatted.' | '.$capex->BudgetRemainingFormatted}}</th>
                                </tr>
                                <tr>
                                    <th>Budget Reserved | Used</th>
                                    <th>: {{$capex->BudgetReservedFormatted.' | '.$capex->BudgetUsedFormatted}}</th>
                                </tr>
                            </table>
                            <br />

                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>List of Detail Capex Allocation</h2>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <table id="data_table" class="table table-bordered responsive-utilities jambo_table">
                                        <thead>
                                            <tr>
                                                <th>Approval No</th>
                                                <th>Project Name</th>
                                                <th>Bdgt. Reserved</th>
                                                <th>Act. Price</th>
                                                <th>Act. Qty</th>
                                                <th>Bdgt. Status</th>
                                                <th>Approval Status</th>
                                                <th>GR Estimation</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <!-- x_content -->
                            </div>
                            <!-- x_panel -->
                        </div>
                    </div>
                    <!-- /Content of Items Shown -->

<!-- End of v3.1 by Ferry, 20150903, Integrate framework -->

