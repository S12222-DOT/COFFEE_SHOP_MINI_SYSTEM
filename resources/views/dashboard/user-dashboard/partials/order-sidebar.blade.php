{{-- <div class="order-sidebar">
    <h2 class="order-title">Order</h2>
    
    <div class="order-items" id="orderItems">
        <div class="empty-cart">
            <p>Your cart is empty</p>
        </div>
    </div>
    
    <div class="order-summary" id="orderSummary" style="display: none;">
        <div class="summary-row">
            <span>Sub Total :</span>
            <span class="summary-value" id="subtotal">$0.00</span>
        </div>
        
        <div class="summary-row">
            <span>Tax (10%) :</span>
            <span class="summary-value" id="tax">$0.00</span>
        </div>
        
        <div class="summary-row total-row">
            <span>Total :</span>
            <span class="summary-value" id="total">$0.00</span>
        </div>
        
        <button class="pay-now-btn" id="payNowBtn">
            Pay Now
        </button>
    </div>
</div> --}}
<form action="{{ route('order.place') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-primary w-100">Pay Now</button>
</form>
