export default (vaultId, relationshipTypeCategoryId) => ({
  dragging: null,
  dropTarget: null,

  startDrag(relationshipTypeId) {
    this.dragging = relationshipTypeId;
  },

  clearDrag() {
    this.dragging = null;
    this.dropTarget = null;
  },

  markDropTarget(position) {
    this.dropTarget = position;
  },

  reorder(position) {
    if (!this.dragging) {
      return;
    }

    this.$refs.reorderForm.action = `/vaults/${vaultId}/adminland/relationship-type-categories/${relationshipTypeCategoryId}/relationship-types/${this.dragging}/position`;
    this.$refs.reorderPosition.value = position;
    this.$refs.reorderForm.requestSubmit();
  },
});
