<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="flash-data" data-flashdata="<?php echo $this->session->flashdata('flash'); ?>"></div>
    <div class="flash-data2" data-flashdata="<?php echo $this->session->flashdata('flash2'); ?>"></div>

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Form Input Commision Voucher</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Commision Voucher</h6>
        </div>

        <div class="card-body">
            <form action="<?= base_url('commisionVoucher') ?>" id="myForm" method="POST">
                <div class="row">
                    <div class="col-lg d-flex justify-content-center mb-3">
                        <h4 style="font-weight:bold">Project Information</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="ProjectNumber">Project Number</label>
                            <?php if (isset($rfq)) : ?>
                                <input type="text" class="form-control" id="projectNumber" name="projectNumber" value="<?= $rfq ?>" readonly>
                            <?php else : ?>
                                <select class="form-control" id="projectNumber" name="projectNumber">
                                    <option value="">-</option>
                                    <?php foreach ($doc as $d) : ?>
                                        <?php if ($this->input->post('projectNumber') == $d['nomor_rfq']) : ?>
                                            <option value="<?= $d['nomor_rfq'] ?>" selected><?= $d['nomor_rfq'] ?> - <?= $d['nama_project'] ?> </option>
                                        <?php else : ?>
                                            <option value="<?= $d['nomor_rfq'] ?>"><?= $d['nomor_rfq'] ?> - <?= $d['nama_project'] ?> </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                            <button class="mt-2 btn btn-primary" type="button" data-toggle="modal" id="buttonRfqModal" data-target="#rfqModal" style="display: none;"><small>View Data</small></button>
                            <?= form_error('projectNumber', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="projectName">Project Name</label>
                            <input type="text" class="form-control" id="projectName" name="projectName" value="<?= $this->input->post('projectName') ? $this->input->post('projectName') : '' ?>" autocomplete="off" required>
                            <?= form_error('projectName', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="internalProjectName">Internal Project Name</label>
                            <input type="text" class="form-control" id="internalProjectName" name="internalProjectName" value="<?= $this->input->post('internalProjectName') ? $this->input->post('internalProjectName') : '' ?>" autocomplete="off" required>
                            <?= form_error('internalProjectName', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="client">Client</label>
                            <input type="text" class="form-control" id="client" name="client" value="" autocomplete="off">
                            <?= form_error('client', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="phoneNumber">Phone Number</label>
                            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="" autocomplete="off">
                            <?= form_error('phoneNumber', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="faxNumber">Fax Number</label>
                            <input type="text" class="form-control" id="faxNumber" name="faxNumber" autocomplete="off" value="" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5 col-project-type">
                        <div class="form-group">
                            <label for="ProjectType">Project Type</label>
                            <select multiple class="form-control" id="projectType" name="projectType[]">
                                <?php foreach ($methodology as $m) : ?>
                                    <option value="<?= $m['methodology'] . ' - ' . $m['keterangan'] ?>"><?= $m['methodology'] . ' - ' . $m['keterangan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error('projectType[]', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-7 col-address">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea type="textarea" class="form-control" id="address" name="address" rows="4" autocomplete="off"></textarea>
                            <?= form_error('address', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                </div>
                <div class="row cp-heading">
                    <div class="col-md">
                        <h5 style="font-weight:bold">Contact Person</h5>
                    </div>
                </div>
                <div class="row cp-body">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="contactPersonName">Name</label>
                            <input type="text" class="form-control" id="contactPersonName" name="contactPersonName[]" value="" autocomplete="off">
                            <?= form_error('contactPersonName[]', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="contactPersonPosition">Position</label>
                            <input type="text" class="form-control" id="contactPersonPosition" name="contactPersonPosition[]" value="" autocomplete="off">
                            <?= form_error('contactPersonPosition[]', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="contactPersonNumber">Phone Number</label>
                            <input type="text" class="form-control" id="contactPersonNumber" name="contactPersonNumber[]" value="" autocomplete="off">
                            <?= form_error('contactPersonNumber[]', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                </div>
                <div class="col text-right"><button type="button" class="tambahCp btn-sm btn-success text-light" style="cursor:pointer;"><i class="fas fa-plus"></i> Tambah</button></div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="hargaPokokProduksi">Harga Pokok Produksi</label>
                            <input type="text" class="form-control subValue" id="hargaPokokProduksi" name="hargaPokokProduksi" value="<?= $this->input->post('hargaPokokProduksi') ? $this->input->post('hargaPokokProduksi') : '0' ?>" autocomplete="off" required>
                            <?= form_error('hargaPokokProduksi', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="managementFee">Management Fee</label>
                            <input type="text" class="form-control subValue" id="managementFee" name="managementFee" value="<?= $this->input->post('managementFee') ? $this->input->post('managementFee') : '0' ?>" autocomplete="off" required>
                            <?= form_error('managementFee', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="valueAddedTax">Value Added Tax (%)</label>
                            <input type="number" class="form-control subValue" id="valueAddedTax" name="valueAddedTax" value="<?= $this->input->post('valueAddedTax') ? $this->input->post('valueAddedTax') : '10' ?>" max="100" min="0" autocomplete="off" required>
                            <?= form_error('valueAddedTax', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="contractValue">Contract Value</label>
                            <input type="text" class="form-control" id="contractValue" name="" value="0" autocomplete="off" readonly>
                            <input type="hidden" class="form-control" id="contractValue2" name="contractValue" value="0" autocomplete="off" readonly>
                            <?= form_error('contractValue', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-md d-flex justify-content-center">
                        <h4 style="font-weight:bold">Payment Details</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <h5 style="font-weight:bold">Invoice</h5>
                    </div>
                </div>
                <div class="row invoice-body">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="termsPayment">Terms of Payment (%)
                                <span class="help-research-brief" style="position: absolute; right: 12px;">
                                    <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                    <span class="tooltiptext" style="width: 200px; box-sizing: border-box;">
                                        <p style="text-align: center; margin: 0 auto;"><b>Jumlah total harus 100%</b></p>
                                    </span>
                                </span>
                            </label>
                            <input type="number" class="form-control termsPayment" id="termsPayment" name="termsPayment[]" value="" autocomplete="off" min="0" max="100" required>
                            <?= form_error('termsPayment[]', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="loa">Based on LOA</label>
                            <input type="text" class="form-control" id="loa" name="loa[]" value="" autocomplete="off" required>
                            <?= form_error('loa[]', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="paymenDetails">Collection Plan Date</label>
                            <input type="date" class="form-control" id="paymentDate" name="paymentDate[]" value="" autocomplete="off" required>
                            <?= form_error('paymentDate[]', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="invoiceDate">Invoice Date</label>
                            <input type="date" class="form-control" id="invoiceDate" name="invoiceDate[]" value="" autocomplete="off" required>
                            <?= form_error('invoiceDate[]', '<small class="text-danger ml-3">', '</small>'); ?>
                        </div>
                    </div>
                </div>
                <div class="col text-right"><button type="button" class="tambahInvoice btn-sm btn-success text-light" style="cursor:pointer;"><i class="fas fa-plus"></i> Tambah</button></div>

                <div class="row mt-4">

                    <div class="col-md">
                        <h5 style="font-weight:bold">LOA or Order Confirmation</h5>
                    </div>
                </div>
                <div class="row">
                    <?php $data_user = $this->User_model->getAllUser(); ?>
                    <div class="col-lg-6">
                        <label for="research Executive">Research Executive</label>
                        <select name="researchExecutive" id="researchExecutive" class="selectpicker show-tick form-control selectAnswerPeserta<?php if (form_error('id_perusahaan')) {
                                                                                                                                                    echo 'is-invalid';
                                                                                                                                                } ?>" data-live-search="true" title="Pilih peserta..." required>
                            <?php
                            foreach ($data_user as $ud) :
                            ?>

                                <?php if (set_value('id_peserta') == $ud['id_user']) { ?>
                                    <option value="<?php echo $ud['id_user'] ?>" selected><?php echo $ud['nama_user'] ?> - <?= $ud['dept']; ?></option>
                                <?php } else { ?>
                                    <option value="<?php echo $ud['id_user'] ?>"><?php echo $ud['nama_user'] ?> - <?= $ud['dept'] ?></option>
                                <?php } ?>
                            <?php endforeach; ?>
                        </select>
                        <!-- <input type="text" class="form-control" id="researchExecutive" name="researchExecutive" value="" autocomplete="off"> -->
                        <?= form_error('researchExecutive', '<small class="text-danger ml-3">', '</small>'); ?>
                    </div>
                    <div class="col-lg-6">
                        <label for="confirmLetter">LOA to be followed up by</label>
                        <select name="confirmLetter" id="confirmLetter" class="selectpicker show-tick form-control selectAnswerPeserta<?php if (form_error('id_perusahaan')) {
                                                                                                                                            echo 'is-invalid';
                                                                                                                                        } ?>" data-live-search="true" title="Pilih peserta...">
                            <?php
                            foreach ($data_user as $ud) : ?>
                                <?php if (set_value('id_peserta') == $ud['id_user']) { ?>
                                    <option value="<?php echo $ud['id_user'] ?>" selected><?php echo $ud['nama_user'] ?> - <?= $ud['dept']; ?></option>
                                <?php } else { ?>
                                    <option value="<?php echo $ud['id_user'] ?>"><?php echo $ud['nama_user'] ?> - <?= $ud['dept']; ?></option>
                                <?php } ?>
                            <?php endforeach; ?>
                        </select>
                        <!-- <input type="text" class="form-control" id="confirmLetter" name="confirmLetter" value="" autocomplete="off"> -->
                    </div>
                </div>
        </div>
        <div class="col-12 text-right form-group">
            <a type="button" class="btn btn-warning text-white" target="_blank" type="submit" id="view" style="display: none;">Preview</a>
            <button class="btn btn-primary" type="button" id="buttonSave">Save</button>
        </div>
        </form>
    </div>
</div>

</div>
<!-- /.container-fluid -->

<!-- Modal -->
<div class="modal fade" id="rfqModal" tabindex="-1" aria-labelledby="rfqModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rfqModalLabel">Data Project</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="form-group col-lg-12">
                    <label for="">Nomor Request</label>
                    <input type="text" class="form-control" id="projectNumberModal" readonly>
                </div>
                <div class="form-group col-lg-6">
                    <label for="">Tanggal Masuk</label>
                    <input type="text" class="form-control" id="enterDateModal" readonly>
                </div>
                <div class="form-group col-lg-6">
                    <label for="">Kode Request</label>
                    <input type="text" class="form-control" id="requestCodeModal" readonly>
                </div>
                <div class="form-group col-lg-6">
                    <label for="">Nama Customer</label>
                    <input type="text" class="form-control" id="customerNameModal" readonly>
                </div>
                <div class="form-group col-lg-6">
                    <label for="">Email Customer</label>
                    <input type="text" class="form-control" id="customerEmailModal" readonly>
                </div>
                <div class="form-group col-lg-12 mt-2">
                    <p class="text-dark researchBrief" id="researchBrief">Research Brief <small><a target='_blank' id="viewResearchBrief" class=' text-primary' style='cursor:pointer;'>View</a></small> - <small><a id="downloadResearchBrief" class=' text-primary' style='cursor:pointer;'>Download</a></small></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const rfq = $('#projectNumber').val();
        checkData(rfq);

        const valueAddedTax = document.querySelector('#valueAddedTax');
        valueAddedTax.addEventListener('keyup', function() {
            countContractValue();
        })
        const hargaPokokProduksi = document.querySelector('#hargaPokokProduksi');
        hargaPokokProduksi.addEventListener('keyup', function() {})
        const managementFee = document.querySelector('#managementFee');
        managementFee.addEventListener('keyup', function() {
            countContractValue();
        })

        const buttonSave = document.querySelector("#buttonSave");
        buttonSave.addEventListener('click', function() {
            const termsPayment = document.querySelectorAll(".termsPayment");
            let total = 0;
            termsPayment.forEach(function(e, i) {
                total += parseInt(e.value);
            })
            console.log(total);
            if (total == 100) {
                $('#myForm').submit();
            } else {
                alert('Total Persentase tidak 100%');
            }
        })

    })

    function countContractValue() {
        const hargaPokokProduksi = parseFloat(document.querySelector('#hargaPokokProduksi').value);
        const managementFee = parseFloat(document.querySelector('#managementFee').value);
        const valueAddedTax = parseFloat(document.querySelector('#valueAddedTax').value) / 100;

        console.log(hargaPokokProduksi);
        console.log(managementFee);
        console.log(valueAddedTax);

        const contractValue = document.querySelector('#contractValue');
        const contractValue2 = document.querySelector('#contractValue2');
        let total = 0;
        total = (hargaPokokProduksi + managementFee) + ((hargaPokokProduksi + managementFee) * valueAddedTax);
        if (isNaN(total)) total = 0;
        contractValue.value = 'Rp. ' + numberWithCommas(total);
        contractValue2.value = total;


    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    const addInvoice = document.querySelector('.tambahInvoice');
    const addCp = document.querySelector('.tambahCp');

    addInvoice.addEventListener('click', function() {
        const divBaru = document.createElement('div');
        countDiv = document.querySelectorAll('.invoice-body');
        if (countDiv.length < 4) {

            divBaru.className = "row invoice-body";
            divBaru.innerHTML = `
             <div class="col-lg-3">
               <div class="form-group">
                 <label for="termsPayment">Terms of Payment (%)</label>
                 <input type="number" class="form-control termsPayment" id="termsPayment` + countDiv.length + `" name="termsPayment[]" value="" autocomplete="off" min="0" max="100">
               </div>
             </div>
             <div class="col-lg-3">
               <div class="form-group">
                 <label for="loa">Based on LOA</label>
                 <input type="text" class="form-control" id="loa` + countDiv.length + `" name="loa[]" value="" autocomplete="off">
               </div>
             </div>
             <div class="col-lg-3">
               <div class="form-group">
                 <label for="paymenDetails">Collection Plan Date</label>
                 <input type="date" class="form-control" id="paymentDate` + countDiv.length + `" name="paymentDate[]" value="" autocomplete="off">
               </div>
             </div>
             <div class="col-lg-3">
               <div class="form-group">
                 <label for="invoiceDate">Invoice Date</label>
                 <input type="date" class="form-control" id="invoiceDate` + countDiv.length + `" name="invoiceDate[]" value="" autocomplete="off">
               </div>
             </div>
             `;
            document.querySelector('.invoice-body').parentElement.insertBefore(divBaru, this.parentElement);
        }
    });

    addCp.addEventListener('click', function() {
        const divBaru = document.createElement('div');
        countDivCp = document.querySelectorAll('.cp-body');
        divBaru.className = "row cp-body";
        divBaru.innerHTML = `
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="contactPerson">Name</label>
                             <input type="text" class="form-control" id="contactPersonName` + countDivCp.length + `" name="contactPersonName[]" value="" autocomplete="off">
                         </div>
                     </div>
                     <div class="col-lg-3">
                         <div class="form-group">
                             <label for="contactPersonPosition">Position</label>
                             <input type="text" class="form-control" id="contactPersonPosition` + countDivCp.length + `" name="contactPersonPosition[]" value="" autocomplete="off">
                         </div>
                     </div>
                     <div class="col-lg-3">
                         <div class="form-group">
                             <label for="contactPerson">Phone Number</label>
                             <input type="text" class="form-control" id="contactPersonNumber` + countDivCp.length + `" name="contactPersonNumber[]" value="" autocomplete="off">
                         </div>
                     </div>
                     `;
        document.querySelector('.cp-body').parentElement.insertBefore(divBaru, this.parentElement);
    });

    $('#projectNumber').change(function() {
        const selectedVal = $('#projectNumber option:selected').val();
        checkData(selectedVal);
    })

    function checkData(rfq) {
        const projectName = '<?= $this->input->post('projectName'); ?>';
        const internalProjectName = '<?= $this->input->post('internalProjectName'); ?>';
        const hargaPokokProduksi = '<?= $this->input->post('hargaPokokProduksi'); ?>';
        const managementFee = '<?= $this->input->post('managementFee'); ?>';
        const valueAddedTax = '<?= $this->input->post('valueAddedTax'); ?>';
        const termsPayment = <?= json_encode($this->input->post('termsPayment')); ?>;
        const loa = <?= json_encode($this->input->post('loa')); ?>;
        const paymentDate = <?= json_encode($this->input->post('paymentDate')); ?>;
        const invoiceDate = <?= json_encode($this->input->post('invoiceDate')); ?>;
        const researchExecutive = '<?= $this->input->post('researchExecutive'); ?>';
        const confirmLetter = '<?= $this->input->post('confirmLetter'); ?>';

        const addInvoice = document.querySelector('.tambahInvoice');
        const addCp = document.querySelector('.tambahCp');


        countDivCp = document.querySelectorAll('.cp-body').length;
        for (let i = 1; i < countDivCp; i++) {
            $('.cp-body-num' + i).remove();
        }
        countDivInvoice = document.querySelectorAll('.invoice-body').length;
        for (let i = 1; i < countDivInvoice; i++) {
            $('.invoice-body-num' + i).remove();
        }

        $('#projectType').find('option:selected').removeAttr('selected');
        $('#projectType').val('0');
        $('#termsPayment').val('');
        $('#loa').val('');
        $('#paymentDate').val('');
        $('#faxNumber').val('');

        $.ajax({
            url: "<?= base_url('commisionVoucher/checkData') ?>",
            type: 'post',
            data: {
                rfq: rfq
            },
            success: function(data) {
                data = JSON.parse(data);

                data.projectName = (projectName) ? projectName : data.projectName;
                data.internalProjectName = (internalProjectName) ? internalProjectName : data.internalProjectName;
                data.hargaPokokProduksi = (hargaPokokProduksi) ? hargaPokokProduksi : data.hargaPokokProduksi;
                data.managementFee = (managementFee) ? managementFee : data.managementFee;
                data.valueAddedTax = (valueAddedTax) ? valueAddedTax : data.valueAddedTax;
                // console.log(termsPayment);
                data.termsPayment = (termsPayment) ? termsPayment : data.termsPayment;
                data.loa = (loa) ? loa : data.loa;
                data.paymentDate = (paymentDate) ? paymentDate : data.paymentDate;
                data.invoiceDate = (invoiceDate) ? invoiceDate : data.invoiceDate;
                data.researchExecutive = (researchExecutive) ? researchExecutive : data.researchExecutive;
                data.confirmLetter = (confirmLetter) ? confirmLetter : data.confirmLetter;

                // console.log(test);
                // console.log(data);
                $('.col-project-type').children().remove()

                $('#projectName').val(data.projectName);
                $('#internalProjectName').val(data.internalProjectName);
                $('#client').val(data.client);
                $('#client').attr('readonly', 'readonly');
                $('#phoneNumber').val(data.phone);
                $('#phoneNumber').attr('readonly', 'readonly');
                $('#faxNumber').val(data.fax);
                $('#faxNumber').attr('readonly', 'readonly');
                let html = `
                        <div class="form-group">
                            <label for="ProjectType">Project Type</label>
                     `;
                $.each(data.projectType, function(i, e) {
                    html += `<input type="text" class="form-control mt-1" name="projectType[]" value="${e}" autocomplete="off" readonly="readonly">
`
                    //  $("#projectType option[value='" + e + "']").prop("selected", true);
                })
                html += `</div>
                        </div>`;
                $('.col-project-type').append(html);
                $('#projectType').attr('readonly', 'readonly');

                $('#address').val(data.address);
                $('#address').attr('readonly', 'readonly');
                countDivCp = document.querySelectorAll('.cp-body');
                for (let i = 0; i < data.contactPersonName.length; i++) {
                    if (i != 0) {
                        let divBaruCp = document.createElement('div');
                        divBaruCp.className = "row cp-body cp-body-num" + i;
                        divBaruCp.innerHTML = `
                   <div class="col-lg-6">
                       <div class="form-group">
                           <label for="contactPersonName">Name</label>
                           <input type="text" class="form-control" id="contactPersonName` + i + `" name="contactPersonName[]" value="${data.contactPersonName[i]}" autocomplete="off" readonly="readonly">
                       </div>
                   </div>
                   <div class="col-lg-3">
                       <div class="form-group">
                           <label for="contactPersonPosition">Position</label>
                           <input type="text" class="form-control" id="contactPersonPosition` + i + `" name="contactPersonPosition[]" value="` + data.jabatan[i] + `" autocomplete="off" readonly="readonly">
                       </div>
                   </div>
                   <div class="col-lg-3">
                       <div class="form-group">
                           <label for="contactPersonNumber">Phone Number</label>
                           <input type="text" class="form-control" id="contactPersonNumber` + i + `" name="contactPersonNumber[]" value="` + data.contactPersonNumber[i] + `" autocomplete="off" readonly="readonly">
                       </div>
                   </div>`;
                        document.querySelector('.cp-body').parentElement.insertBefore(divBaruCp, addCp.parentElement);
                    } else {
                        $('#contactPersonName').val(data.contactPersonName[i]);
                        $('#contactPersonName').attr('readonly', 'readonly');
                        $('#contactPersonPosition').val(data.jabatan[i]);
                        $('#contactPersonPosition').attr('readonly', 'readonly');
                        $('#contactPersonNumber').val(data.contactPersonNumber[i]);
                        $('#contactPersonNumber').attr('readonly', 'readonly');
                    }
                }

                $('#contractValue').val('Rp. ' + numberWithCommas(data.contractValue));
                $('#contractValue2').val(data.contractValue);
                if (data.valueAddedTax) {
                    $('#valueAddedTax').val(data.valueAddedTax);
                } else {
                    $('#valueAddedTax').val(10);
                }
                $('#hargaPokokProduksi').val(data.hargaPokokProduksi);
                $('#managementFee').val(data.managementFee);
                for (let i = 0; i < data.termsPayment.length; i++) {
                    console.log(i)
                    if (i != 0) {
                        const divBaru = document.createElement('div');

                        divBaru.className = "row invoice-body invoice-body-num" + i;
                        divBaru.innerHTML = `
                               <div class="col-lg-3">
                                 <div class="form-group">
                                   <label for="termsPayment">Terms of Payment (%)</label>
                                   <input type="number" class="form-control termsPayment" id="termsPayment` + i + `" name="termsPayment[]" value="` + data.termsPayment[i] + `" autocomplete="off" min="0" max="100">
                                 </div>
                               </div>
                               <div class="col-lg-3">
                                 <div class="form-group">
                                   <label for="loa">Based on LOA</label>
                                   <input type="text" class="form-control" id="loa` + i + `" name="loa[]" value="` + data.loa[i] + `" autocomplete="off">
                                 </div>
                               </div>
                               <div class="col-lg-3">
                                 <div class="form-group">
                                   <label for="paymenDetails">Collection Plan Date</label>
                                   <input type="date" class="form-control" id="paymentDate` + i + `" name="paymentDate[]" value="` + data.paymentDate[i] + `" autocomplete="off">
                                 </div>
                               </div>
                               <div class="col-lg-3">
                                 <div class="form-group">
                                   <label for="invoiceDate">Invoice Date</label>
                                   <input type="date" class="form-control" id="invoiceDate` + i + `" name="invoiceDate[]" value="` + data.invoiceDate[i] + `" autocomplete="off">
                                 </div>
                               </div>
                               `;

                        document.querySelector('.invoice-body').parentElement.insertBefore(divBaru, addInvoice.parentElement);
                    } else {
                        $('#termsPayment').val(data.termsPayment[i]);
                        $('#loa').val(data.loa[i]);
                        $('#paymentDate').val(data.paymentDate[i]);
                        $('#invoiceDate').val(data.invoiceDate[i]);
                    }
                }
                //  console.log(data.researchExecutive);
                $('#researchExecutive').val(data.researchExecutive);
                $('#confirmLetter').val(data.confirmLetter);
                $('.selectpicker').selectpicker('refresh');

                if (data.contractValue) {
                    document.querySelector('#buttonRfqModal').style.display = 'inline';
                    const view = document.querySelector('#view');
                    view.style.display = 'inline';
                    view.href = '<?= base_url('projectDocument/printPdf/') ?>' + rfq + '?status=view';
                    updateModal(data.dataRfq);
                }
            }
        })
    }

    function updateModal(dataRfq) {
        $('#projectNumberModal').val(dataRfq.projectNumber);
        $('#enterDateModal').val(dataRfq.enterDate);
        $('#requestCodeModal').val(dataRfq.projectCode);
        $('#customerNameModal').val(dataRfq.customerName);
        $('#customerEmailModal').val(dataRfq.emailCustomer);
        const researchBrief = document.querySelector('#researchBrief');

        var base_url = window.location.origin;
        if (!dataRfq.idResearchBrief) {
            researchBrief.parentNode.style.display = 'none';
        } else {
            researchBrief.parentNode.style.display = 'inline';
            document.querySelector('#viewResearchBrief').setAttribute("href", `${base_url}/dev-digital-market/researchBrief/printPdf/${dataRfq.idResearchBrief}?status=view&rfq=${dataRfq.projectNumber}`);
            document.querySelector('#downloadResearchBrief').setAttribute("href", `${base_url}/dev-digital-market/researchBrief/printPdf/${dataRfq.idResearchBrief}?status=download&rfq=${dataRfq.projectNumber}`);
        }
    }
</script>