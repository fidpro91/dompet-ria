<ul class="conversation-list slimscroll">
    <div class="simplebar-content">
        @foreach($results as $rs)
        <li>
            <div class="message-list">
                <div class="conversation-text" style="margin-bottom: 15px;">
                    <div class="ctext-wrap">
                        <span class="user-name" style="font-weight: bold !important;">
                            {{$rs->userReport->name}}
                        </span>
                        <p>
                            Skor a.n. {{$rs->employee->emp_name}}.<br>
                            {{$rs->isi_komplain}}
                        </p>
                    </div>
                </div>
            </div>
        </li>
        @if($rs->tanggapan_komplain)
        <li class="odd">
            <div class="message-list">
                <div class="conversation-text">
                    <div class="ctext-wrap">
                        <span class="user-name" style="font-weight: bold !important;">ADMIN</span>
                        <p>
                            {{$rs->tanggapan_komplain}}
                        </p>
                    </div>
                </div>
            </div>
        </li>
        @endif
        @endforeach
    </div>
</ul>