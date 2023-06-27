function paramTable(subdir, page, prefix, id) {
    $.ajax({
        url: subdir + '/' + page + '/paramTable',
        type: "POST",
        data: {
            prefix: prefix,
            id: id
        },
        error: function () {
            alert("param table error");
        },
        success: function (data) {
            data = JSON.parse(data);
            var table = '<table class="table table-condensed table-bordered" style="width: 90%; text-align: right;"><tbody>'
            data.forEach(function (d) {
                if (d[0] != "created_at") {
                    d[1] = d[1].replace(/:/g, ', ');
                }
                table += '<tr><td>' + d[0] + '</td><td>' + d[1] + '</td></tr>'
            })
            table += '</tbody></table>'
            $('#paramTable').html(table);
        }
    });
}

function sumTable(subdir, page, prefix, id) {
    $.ajax({
        url: subdir + '/' + page + '/sumTable',
        type: "POST",
        data: {
            prefix: prefix,
            id: id
        },
        success: function (data) {
            $('#sumTable').append(data);
        }
    });
}

function showResultTables(prefix, id, posMap, eqtlMap, ciMap, orcol, becol, secol) {
    $('#plotClear').hide();
    $('#download').attr('disabled', false);
    if (eqtlMap == 0) {
        $('#eqtlTableTab').hide();
        $('#check_eqtl_annotPlot').hide();
        $('#annotPlot_eqtl').prop('checked', false);
        $('#eqtlfiledown').hide();
        $('#eqtlfile').prop('checked', false);
    }

    if (ciMap == 0) {
        $('#ciTableTab').hide();
        $('#check_ci_annotPlot').hide();
        $('#annotPlot_ci').prop('checked', false);
        $('#cifiledown').hide();
        $('#cifile').prop('checked', false);
    }

    var lociTable = $('#lociTable').DataTable({
        "processing": true,
        serverSide: false,
        select: true,
        "ajax": {
            url: "DTfile",
            type: "POST",
            data: {
                id: id,
                prefix: prefix,
                infile: "GenomicRiskLoci.txt",
                header: "GenomicLocus:uniqID:rsID:chr:pos:p:start:end:nSNPs:nGWASSNPs:nIndSigSNPs:IndSigSNPs:nLeadSNPs:LeadSNPs"
            }
        },
        error: function () {
            alert("GenomicRiskLoci table error");
        },
        "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "iDisplayLength": 10
    });

    var leadTable = $('#leadSNPtable').DataTable({
        "processing": true,
        serverSide: false,
        select: true,
        "ajax": {
            url: "DTfile",
            type: "POST",
            data: {
                id: id,
                prefix: prefix,
                infile: "leadSNPs.txt",
                header: "No:GenomicLocus:uniqID:rsID:chr:pos:p:nIndSigSNPs:IndSigSNPs"
            }
        },
        error: function () {
            alert("sigSNPs table error");
        },
        "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "iDisplayLength": 10,
    });

    var IndSigTable = $('#sigSNPtable').DataTable({
        "processing": true,
        serverSide: false,
        select: true,
        "ajax": {
            url: "DTfile",
            type: "POST",
            data: {
                id: id,
                prefix: prefix,
                infile: "IndSigSNPs.txt",
                header: "No:GenomicLocus:uniqID:rsID:chr:pos:p:nSNPs:nGWASSNPs"
            }
        },
        error: function () {
            alert("sigSNPs table error");
        },
        "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "iDisplayLength": 10,
    });

    var table = "<thead>"
        + "<tr>"
        + "<th>uniqID</th><th>rsID</th><th>chr</th><th>pos</th><th>non_effect_allele</th><th>effect_allele</th><th>MAF</th><th>gwasP</th>";
    var cols = "uniqID:rsID:chr:pos:non_effect_allele:effect_allele:MAF:gwasP";
    var cadd_col = 14;
    if (orcol != "NA") {
        table += "<th>OR</th>";
        cols += ":or";
        cadd_col += 1;
    }
    if (becol != "NA") {
        table += "<th>Beta</th>";
        cols += ":beta";
        cadd_col += 1;
    }
    if (secol != "NA") {
        table += "<th>SE</th>";
        cols += ":se";
        cadd_col += 1;
    }
    table += "<th>Genomic Locus</th><th>r2</th><th>IndSigSNP</th><th>Nearest gene</th><th>dist</th><th>position</th><th>CADD</th><th>RDB</th><th>minChrState(127)</th><th>commonChrState(127)</th>"
        + "</tr>"
        + "</thead>";
    cols += ":GenomicLocus:r2:IndSigSNP:nearestGene:dist:func:CADD:RDB:minChrState:commonChrState";

    $('#SNPtable').html(table)
    var SNPtable = $('#SNPtable').DataTable({
        processing: true,
        serverSide: false,
        select: false,
        ajax: {
            url: 'DTfile',
            type: "POST",
            data: {
                id: id,
                prefix: prefix,
                infile: "snps.txt",
                header: cols
            }
        },
        error: function () {
            alert("SNP table error");
        },
        "columnDefs": [
            { type: "scientific", targets: 7 },
            { type: "num", targets: cadd_col }
        ],
        "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "iDisplayLength": 10
    });

    var annovTable = $('#annovTable').DataTable({
        processing: true,
        serverSide: false,
        select: false,
        ajax: {
            url: 'DTfile',
            type: "POST",
            data: {
                id: id,
                prefix: prefix,
                infile: "annov.txt",
                header: "uniqID:chr:pos:gene:symbol:dist:annot:exonic_func:exon"
            }
        },
        "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "iDisplayLength": 10
    });

    var table = "<thead><tr><th>Gene</th><th>Symbol</th><th>HUGO</th><th>entrezID</th><th>chr</th><th>start</th><th>end</th>";
    table += "<th>strand</th><th>type</th><th>pLI</th><th>ncRVIS</th>";
    var col = "ensg:symbol:HUGO:entrezID:chr:start:end:strand:type:pLI:ncRVIS";
    if (posMap == 1) {
        table += "<th>posMapSNPs</th><th>posMapMaxCADD</th>";
        col += ":posMapSNPs:posMapMaxCADD";
    }
    if (eqtlMap == 1) {
        table += "<th>eqtlMapSNPs</th><th>eqtlMapminP</th><th>eqtlMapminQ</th><th>eqtlMapts</th><th>eqtlDirection</th>";
        col += ":eqtlMapSNPs:eqtlMapminP:eqtlMapminQ:eqtlMapts:eqtlDirection";
    }
    if (ciMap == 1) {
        table += "<th>ciMap</th><th>ciMapts</th>";
        col += ":ciMap:ciMapts";
    }
    table += "<th>minGwasP</th><th>Genomic Locus</th><th>IndSigSNPs</th></tr></thead>";
    col += ":minGwasP:GenomicLocus:IndSigSNPs"
    $('#geneTable').append(table);
    var geneTable;
    geneTable = $('#geneTable').DataTable({
        processing: true,
        serverSide: false,
        select: false,
        ajax: {
            url: 'DTfile',
            type: "POST",
            data: {
                id: id,
                prefix: prefix,
                infile: "genes.txt",
                header: col
            }
        },
        "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "iDisplayLength": 10
    });

    if (eqtlMap == 1) {
        var eqtlTable = $('#eqtlTable').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 3000,
            select: false,
            ajax: {
                url: 'DTfileServerSide',
                type: "POST",
                data: {
                    id: id,
                    prefix: prefix,
                    infile: "eqtl.txt",
                    header: "uniqID:chr:pos:testedAllele:db:tissue:gene:symbol:p:FDR:signed_stats:RiskIncAllele:alignedDirection"
                }
            },
            "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "iDisplayLength": 10
        });
    }

    if (ciMap == 1) {
        var ciTable = $('#ciTable').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 3000,
            select: false,
            ajax: {
                url: 'DTfileServerSide',
                type: "POST",
                data: {
                    id: id,
                    prefix: prefix,
                    infile: "ci.txt",
                    header: "GenomicLocus:region1:region2:FDR:type:DB:tissue/cell:inter/intra:SNPs:genes"
                }
            },
            "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "iDisplayLength": 10
        });

        var ciSNPsTable = $('#ciSNPsTable').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 3000,
            select: false,
            ajax: {
                url: 'DTfileServerSide',
                type: "POST",
                data: {
                    id: id,
                    prefix: prefix,
                    infile: "ciSNPs.txt",
                    header: "uniqID:rsID:chr:pos:reg_region:type:tissue/cell"
                }
            },
            "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "iDisplayLength": 10
        });

        var ciGenesTable = $('#ciGenesTable').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 3000,
            select: false,
            ajax: {
                url: 'DTfileServerSide',
                type: "POST",
                data: {
                    id: id,
                    prefix: prefix,
                    infile: "ciProm.txt",
                    header: "region2:reg_region:type:tissue/cell:genes"
                }
            },
            "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "iDisplayLength": 10
        });
    }

    var gwascatTable = $('#gwascatTable').DataTable({
        processing: true,
        serverSide: false,
        select: false,
        ajax: {
            url: 'DTfile',
            type: "POST",
            data: {
                id: id,
                prefix: prefix,
                infile: "gwascatalog.txt",
                header: "GenomicLocus:IndSigSNP:chr:bp:snp:PMID:Trait:FirstAuth:Date:P"
            }
        },
        "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "iDisplayLength": 10
    });

    $('#sigSNPtable tbody').on('click', 'tr', function () {
        $('#plotClear').show();
        $('#annotPlotPanel').show();
        $('#annotPlotSelect').val('IndSigSNP');
        var rowI = IndSigTable.row(this).index();
        sigSNPtable_selected = rowI;
        $('#annotPlotRow').val(rowI);
        Chr15Select();
        d3.select('#locusPlot').select("svg").remove();
        var rowData = IndSigTable.row(rowI).data();
        var chr = rowData[4];

        $.ajax({
            url: subdir + '/' + page + '/locusPlot',
            type: "POST",
            data: {
                type: "IndSigSNP",
                id: id,
                prefix: prefix,
                rowI: rowI
            },
            success: function (data) {
                var plotData = JSON.parse(data.replace(/NaN/g, "-1"));
                locusPlot(plotData, "IndSigSNP", chr);
            }
        });

        $('#selectedLeadSNP').html("");
        var out = "<h5>Selected Ind. Sig. SNP</h5><table class='table table-striped'><tr><td>Ind. Sig. SNP</td><td>" + rowData[3]
            + "</td></tr><tr><td>Chrom</td><td>" + rowData[4] + "</td></tr><tr><td>BP</td><td>"
            + rowData[5] + "</td></tr><tr><td>P-value</td><td>" + rowData[6] + "</td></tr><tr><td>SNPs within LD</td><td>"
            + rowData[7] + "</td></tr><tr><td>GWAS SNPs within LD</td><td>" + rowData[8] + "</td></tr>";
        $('#selectedLeadSNP').html(out);
    });

    $('#leadSNPtable tbody').on('click', 'tr', function () {
        $('#plotClear').show();
        $('#annotPlotPanel').show();
        $('#annotPlotSelect').val('leadSNP');
        var rowI = leadTable.row(this).index();
        sigSNPtable_selected = rowI;
        $('#annotPlotRow').val(rowI);
        Chr15Select();
        d3.select('#locusPlot').select("svg").remove();
        var rowData = leadTable.row(rowI).data();
        var chr = rowData[4];

        $.ajax({
            url: subdir + '/' + page + '/locusPlot',
            type: "POST",
            data: {
                type: "leadSNP",
                id: id,
                prefix: prefix,
                rowI: rowI
            },
            success: function (data) {
                var plotData = JSON.parse(data.replace(/NaN/g, "-1"));
                locusPlot(plotData, "leadSNP", chr);
            }
        });

        $('#selectedLeadSNP').html("");
        var out = "<h5>Selected lead SNP</h5><table class='table table-striped'><tr><td>Lead SNP</td><td>" + rowData[3]
            + "</td></tr><tr><td>Chrom</td><td>" + rowData[4] + "</td></tr><tr><td>BP</td><td>"
            + rowData[5] + "</td></tr><tr><td>P-value</td><td>" + rowData[6] + "</td></tr>"
            + "<tr><td>#Ind. Sig. SNPs</td><td>" + rowData[7] + "</td></tr>";
        $('#selectedLeadSNP').html(out);
    });

    $('#lociTable tbody').on('click', 'tr', function () {
        $('#plotClear').show();
        $('#annotPlotPanel').show();
        $('#annotPlotSelect').val('GenomicLocus');
        var rowI = lociTable.row(this).index();
        lociTable_selected = rowI;
        $('#annotPlotRow').val(rowI);
        Chr15Select();
        d3.select('#locusPlot').select("svg").remove();
        var rowData = lociTable.row(rowI).data();
        var chr = rowData[3];

        $.ajax({
            url: subdir + '/' + page + '/locusPlot',
            type: "POST",
            data: {
                type: "loci",
                id: id,
                prefix: prefix,
                rowI: rowI
            },
            success: function (data) {
                var plotData = JSON.parse(data.replace(/NaN/g, "-1"));
                locusPlot(plotData, "loci", chr);
            }
        });

        $('#selectedLeadSNP').html("");
        var out = "<h5>Selected Locus</h5><table class='table table-striped'><tr><td>top lead SNP</td><td>" + rowData[2]
            + "</td></tr><tr><td>Chrom</td><td>" + rowData[3] + "</td></tr><tr><td>BP</td><td>"
            + rowData[4] + "</td></tr><tr><td>P-value</td><td>" + rowData[5] + "</td></tr>"
            + "<tr><td>#Ind. Sig. SNPs</td><td>" + rowData[10] + "</td></tr><tr><td>#lead SNPs</td><td>" + rowData[12]
            + "</td></tr><tr><td>SNPs within LD</td><td>"
            + rowData[8] + "</td></tr><tr><td>GWAS SNPs within LD</td><td>" + rowData[9] + "</td></tr>";

        $('#selectedLeadSNP').html(out);
    });
}
