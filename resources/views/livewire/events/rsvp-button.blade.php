<div>
  @if($isJoined)
    <button wire:click="toggle" class="btn btn-sm btn-outline btn-error">
      参加を取り消す
    </button>
  @elseif($isFull)
    <button class="btn btn-sm btn-disabled" disabled>
      満席
    </button>
  @else
    <button wire:click="toggle" class="btn btn-sm btn-outline btn-primary">
      参加する
    </button>
  @endif
</div>
