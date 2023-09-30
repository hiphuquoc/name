<form id="formSearch" method="get" action="{{ route('main.test') }}"> 
    <textarea name="input" rows="5" style="width:100%"></textarea>
    <button type="submit">Chuyển</button>
</form>
<div>{{ $result ?? null}}</div>