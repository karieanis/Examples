package com.examples.hive.udaf;

import org.apache.hadoop.hive.ql.exec.UDAF;
import org.apache.hadoop.hive.ql.exec.UDAFEvaluator;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.LinkedHashSet;
import java.util.Set;

/**
 * This UDAF can be used to group columns within n number of rows into key / value paired map eg.
 * SELECT _col1, GROUP_MAP(_col2, _col3) FROM table GROUP BY _col1
 * 
 * Would result in
 * _col1	-> 		{_col2: _col3, _col2: _col3}
 * n		->		n
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
public final class GroupMap extends UDAF {
    /**
     * @author Jeremy Rayner <jeremy@davros.com.au>
     *
     */
    public static class StringKeyIntegerValueEvaluator implements UDAFEvaluator {
    	/**
    	 * 
    	 */
    	private HashMap<String, Integer> buffer;
    	
    	public StringKeyIntegerValueEvaluator() {
    		super();
    		init();
    	}
    	
		/* (non-Javadoc)
		 * @see org.apache.hadoop.hive.ql.exec.UDAFEvaluator#init()
		 */
		public void init() {
			buffer = new HashMap<String, Integer>();
		}
    	
		/**
		 * @param key
		 * @param value
		 * @return boolean
		 */
		public boolean iterate(String key, Integer value) {
			if(!buffer.containsKey(key)) {
				buffer.put(key, value);
			}
			
			return true;
		}
		
		/**
		 * @return HashMap<String, Integer>
		 */
		public HashMap<String, Integer> terminatePartial() {
			return buffer;
		}
		
		/**
		 * @param another
		 * @return boolean
		 */
		public boolean merge(HashMap<String, Integer> another) {
			if(another == null) {
				return true;
			}
			
			for(final String key: another.keySet()) {
				iterate(key, another.get(key));
			}
			
			return true;
		}
		
		/**
		 * @return HashMap<String, Integer>
		 */
		public HashMap<String, Integer> terminate() {
			if(buffer.size() == 0) {
				return null;
			}
			
			return buffer;
		}
    }
    
    /**
     * @author Jeremy Rayner <jeremy@davros.com.au>
     *
     */
    public static class StringKeyArrayValueEvaluator implements UDAFEvaluator {
    	/**
    	 * 
    	 */
    	private HashMap<String, ArrayList<Integer>> buffer;
    	
    	public StringKeyArrayValueEvaluator() {
    		super();
    		init();
    	}
    	
    	/* (non-Javadoc)
    	 * @see org.apache.hadoop.hive.ql.exec.UDAFEvaluator#init()
    	 */
    	public void init() {
    		buffer = new HashMap<String, ArrayList<Integer>>();
    	}
    	
    	/**
    	 * @param key
    	 * @param value
    	 * @return boolean
    	 */
		public boolean iterate(String key, ArrayList<Integer> value) {
    		if(!buffer.containsKey(key)) {
    			buffer.put(key, copy(value));
    		} else {
    			final ArrayList<Integer> current = buffer.get(key); // get the current list
    			final Set<Integer> another = new LinkedHashSet<Integer>(current); // removes dupes and maintains ordering
    			another.addAll(copy(value)); // add the incoming value to the set

    			current.clear(); // clear the current list
    			current.addAll(another); // add the de-duped ordered list back in
    			
    			buffer.remove(key);		  // make sure the current object has been removed
    			buffer.put(key, current); // put it back into the buffer
    		}

    		return true;
    	}
    	
    	/**
    	 * @return HashMap<String, ArrayList<Integer>>
    	 */
    	public HashMap<String, ArrayList<Integer>> terminatePartial() {
    		return buffer;
    	}
    	
    	/**
    	 * @param another
    	 * @return boolean
    	 */
    	public boolean merge(HashMap<String, ArrayList<Integer>> another) {
    		if(another == null) {
    			return true;
    		}

    		for(final String key: another.keySet()) {
    			iterate(key, another.get(key));
    		}
    		
    		return true;
    	}
    	
    	/**
    	 * @return HashMap<String, ArrayList<Integer>>
    	 */
    	public HashMap<String, ArrayList<Integer>> terminate() {
    		if(buffer.size() == 0) {
    			return null;
    		}
    		
    		return buffer;
    	}
    
    	/**
    	 * @param list
    	 * @return ArrayList<Integer>
    	 */
    	public ArrayList<Integer> copy(ArrayList<Integer> list) {
    		return new ArrayList<Integer>(list);
    	}
    }
}
