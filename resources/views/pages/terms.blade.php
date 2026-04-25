@extends('layouts.auth')
@php
    use App\Models\Utility;
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $settings = Utility::settings();
    $company_logo = $settings['company_logo'] ?? '';
@endphp

@section('page-title')
    {{ __('Terms and Conditions') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <img style="width: 180px;" src="{{ asset('assets/assestsnew/logo.png') }}" alt="">
            </div>
            <div class="col-12 main_content_terms pb-5">
            <div class="row justify-content-center">
            <div class="col-12 text-center my-5">
            <h1>Terms &amp; Conditions</h1>
            </div>
            <div class="col-lg-10 col-11">
            <div class="row">
            <div class="col-12">
            <div class="row">
            <h3>QUOTATIONS AND ESTIMATES</h3>
            <p>All quotations and estimates gave by <b>MS IT PARK Pvt Ltd</b> are legitimate for a time of 30 days from date of issue. Citations not acknowledged inside this time span must be re-given.</p>
            <p>All quotations are needed to be acknowledged utilizing the provided Quotation Acceptance Form and got back to <b>MS IT PARK Pvt Ltd</b> inside the 30 day time span from date of issuance.</p>
            <p>All provided cost estimates, barring where demonstrated, do not include Goods and Services Tax.</p>
            <p>Estimates might be given by <b>MS IT PARK Pvt Ltd</b> to offer the customer a guide on the projected costing of an undertaking before any revelation or exploration for said project. All estimates will be obviously set apart all things considered and are not an indication of the exact final cost to develop the application.</p>
            <p>All appraisals should be formalized to a quotation or receipt before acknowledgment by one or the other party as the last expense of the application.</p>
            <p><b>MS IT PARK Pvt Ltd</b> maintains all authority to suspend the services/quotation whenever, with no earlier data.</p>
            </div>
            </div>
            <div class="col-12">
            <div class="row">
            <h3>PAYMENT TERMS</h3>
            <p>All quotations provided by <b>MS IT PARK Pvt Ltd</b>, require a 50% deposit upon acceptance.</p>
            <p>Unless prior arrangement has been made, final payment is strictly net 10 days from the date of completion.</p>
            <p>Any cost arising from payment clearings or transaction charges are solely the responsibility of the client and will be charged as such.</p>
            <p><b>MS IT PARK Pvt Ltd</b> will commence work on the quoted application once any deposited funds have cleared.</p>
            <p>The customer will not be entitled for any service in case of delay in payment for more than 10 days from the final date of installation / date of project / module completion.</p>
            <p>If opted for service beyond the 12 months of maintenance period or as agreed by <b>MS IT PARK Pvt Ltd</b>, The Annual Maintenance Charges (AMC) will be normally applicable 50% (PERCENT) of original development cost of Project / Module; each year the development cost for new modules will be added to the initial development cost for the calculation of the AMC.</p>
            <p>The AMC percentage shall be decided by the <b>MS IT PARK Pvt Ltd</b>; which depends upon the amount of efforts and work required. This % may vary each year.</p>
            <p><b>MS IT PARK Pvt Ltd</b> disclaims all warranties or conditions, whether expressed or implied, (including without limitation implied, warranties or conditions of information and context). We consider ourselves and intend to be subject to the jurisdiction of India.</p>
            </div>
            </div>
            <div class="col-12">
            <div class="row">
            <h3>CANCELLATIONS</h3>
            <p>Should the client wish to cancel acceptance of the quotation, <b>MS IT PARK Pvt Ltd</b> will invoice the client for any work completed to date, as a percentage of the total work involved.</p>
            <p>The minimum cancellation fee will be 30% of the signed quotation.</p>
            </div>
            </div>
            <div class="col-12">
            <div class="row">
            <h3>CONTENT</h3>
            <p>Clients are required to ensure that the content of the Web | App.</p>
            <p>The client shall further indemnify <b>MS IT PARK Pvt Ltd</b> in respect of any claims, costs and expenses that may arise from any material included within the quoted application by <b>MS IT PARK Pvt Ltd</b> at the client’s request.</p>
            <p><b>MS IT PARK Pvt Ltd</b> reserves the right not to include any material supplied by the client within the quoted application if <b>MS IT PARK Pvt Ltd</b> deems said material inappropriate of offensive.</p>
            <p>The client shall further indemnify <b>MS IT PARK Pvt Ltd</b> in respect of any claims, costs and expenses that may arise from any material included within the quoted application by <b>MS IT PARK Pvt Ltd</b> at the client’s request.</p>
            <p><b>MS IT PARK Pvt Ltd</b> will not populate the application with the final content unless said content is delivered to <b>MS IT PARK Pvt Ltd</b> in digital format prior to commencement of work. Said content, if available, will be used for testing purposes and may not be formatted how the client requires it. If content is not available mock placeholder content will be used.</p>
            <p>It is the client's responsibility, in all cases, to ensure the applications content is displayed and formatted as they require. If the client cannot format the applications content, <b>MS IT PARK Pvt Ltd</b> will offer this service at <b>MS IT PARK Pvt Ltd</b> current hourly rate at the time of the request.</p>
            </div>
            </div>
            <div class="col-12">
            <div class="row">
            <h3>PERMISSIONS AND COPYRIGHTS</h3>
            <p>The client will obtain all necessary permissions and authorities with respect to the use of all copies, graphics, logos, names and trademarks, and any other material supplied by the client to <b>MS IT PARK Pvt Ltd</b>.</p>
            <p>Supply of said material by the client to <b>MS IT PARK Pvt Ltd</b> shall be regarded as a guarantee from the client that all such permissions and authorities have been sought and obtained for said material.</p>
            <p>No responsibility will be accepted by <b>MS IT PARK Pvt Ltd</b> for damages or losses incurred by the client from the use of material for which permission or authority has not been obtained.</p>
            </div>
            </div>
            <div class="col-12">
            <div class="row">
            <h3>ERRORS AND LIABILITIES</h3>
            <p><b>MS IT PARK Pvt Ltd</b> will pursue due care to ensure applications created by <b>MS IT PARK Pvt Ltd</b> are free of errors.</p>
            <p><b>MS IT PARK Pvt Ltd</b> will correct any errors made by <b>MS IT PARK Pvt Ltd</b> staff in undertaking the quoted application.</p>
            <p><b>MS IT PARK Pvt Ltd</b> does not accept responsibility for losses or damage arising from errors within any application.</p>
            <p><b>MS IT PARK Pvt Ltd</b> does not accept responsibility for errors, damages, losses, or additional costs that relate to third-party products that <b>MS IT PARK Pvt Ltd</b> may require to complete the quoted application.</p>
            </div>
            </div>
            <div class="col-12">
            <div class="row">
            <h3>ALTERATIONS</h3>
            <p>Any alterations requested by the client after development has begun will incur extra development and regression testing time. Depending upon the alteration or change requested an average of 3 days of extra development time per alteration should be allowed for. The 3-day average may not be indicative of the time required and can be extended commensurate with the time involved to implement said changes.</p>
            <p><b>MS IT PARK Pvt Ltd</b> will not accept responsibility for any alterations performed by the client or any third party which may cause or induce errors within the quoted application.</p>
            <p>If <b>MS IT PARK Pvt Ltd</b> is required to correct said alterations or errors resulting from said alterations, induced, injected, or otherwise caused by parties other than <b>MS IT PARK Pvt Ltd</b>, the client will be charged at the hourly rate that is current for <b>MS IT PARK Pvt Ltd</b> at the time said errors are to be fixed.</p>
            </div>
            </div>
            <div class="col-12">
            <div class="row">
            <h3>COMPLETION OF WORK</h3>
            <p>All time frames offered by <b>MS IT PARK Pvt Ltd</b> to the client are estimates. The intrinsic nature of website development and its intricacies do not offer <b>MS IT PARK Pvt Ltd</b> the luxury of defining definite time frames.</p>
            <p><b>MS IT PARK Pvt Ltd</b> will endeavor to complete all work within the estimated timeframes discussed with the client in the quotation. However, <b>MS IT PARK Pvt Ltd</b> will not be liable for any penalties, monies, or hardships otherwise incurred by the client if the application cannot be delivered within the estimated time frame.</p>
            <p><b>MS IT PARK Pvt Ltd</b> will not release the quoted application unless all payments have been met under the obligations of the quotation or work agreement.</p>
            <p>The quoted application remains the property of <b>MS IT PARK Pvt Ltd</b> web | app until all obligations have been met for the release of said application to the client.</p>
            <p>If <b>MS IT PARK Pvt Ltd</b> is working as a third party to another company, said the company is responsible for meeting the obligations for the release of the quote application to their client.</p>
            </div>
            </div>
            <div class="col-12">
            <div class="row">
            <h3>CHANGES TO SITE AND THESE TERMS AND CONDITIONS</h3>
            <p>This Site and these <b>MS IT PARK Pvt Ltd</b> Terms and Conditions may be amended, revised, changed, updated, or modified by <b>MS IT PARK Pvt Ltd</b> with or without notice. Please review this link on a regular basis for changes. Continued use of this Site following any change to the <b>MS IT PARK Pvt Ltd</b> Terms and Conditions constitutes your acceptance of any such change to the <b>MS IT PARK Pvt Ltd</b> Terms and Conditions.</p>
            </div>
            </div>
            </div>
            </div>
            </div>
            </div>
        </div>
    </div>
@endsection
