<?php

namespace skyblock\items\customenchants;

final class CustomEnchantIdentifier {

    private string $id;
    private string $name;
    private string $important;
	private bool $stackable ;
	private int $intIdentifier = 0; //used to save the ce in pmmps item enchantment saving system

    public function __construct(string $id, string $name, bool $important = true, bool $stackable = false) {
        $this->id = $id;
        $this->name = $name;
        $this->important = $important;
		$this->stackable = $stackable;


		//basically, adding up all the ascii value of the chars in the string identifier. This results in an unique number
		foreach(str_split($id) as $char){
			$this->intIdentifier = ord($char);
		}
    }

    public function getId(): string {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getFullName(): string {
        return $this->id . ":" . $this->name;
    }

	public function isStackable() : bool{
		return $this->stackable;
	}

	public function getIntIdentifier() : int{
		return $this->intIdentifier;
	}

    /**
     * @return bool
     * If called true the CustomEnchant will be "important" meaning it will show
     * an activate-message to the player when an Event is called such as equipping
     * an Item with the CustomEnchant on, or hitting another player with an Item
     * that has the CustomEnchant applied onto it.
     */
    public function isImportant(): bool {
        return $this->important;
    }

}